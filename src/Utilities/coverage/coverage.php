<?php

$outputFile = __DIR__ . '/coverage.html';

$suites = ["@core"];

function rglob($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

$scenariosInFeatureAndSuite = null;

foreach (rglob(__DIR__ . "/../../Features/*.feature") as $scenarioFile) {
    $lineNumber = 0;
    $localSuite = null;
    $globalSuite = null;
    $isDisabled = false;
    $disabledLineNumber = 0;
    $currentFeature = "Undefined Feature";
    foreach (preg_split("/((\r?\n)|(\r\n?))/", file_get_contents($scenarioFile)) as $line) {
        if ($lineNumber++ > $disabledLineNumber) {
            $isDisabled = false;
        }
        if ((strpos($line, "@wip") !== false || strpos($line, "@unstable") !== false) && strpos($line, "@unstable_") === false) {
            if ($lineNumber == 1) {
                break;
            } else {
                $isDisabled = true;
                $disabledLineNumber = $lineNumber;
                continue;
            }
        }
        foreach ($suites as $suite) {
            if (strpos($line, $suite) !== false) {
                if ($lineNumber == 1) {
                    $globalSuite = $suite;
                    continue;
                } else {
                    $localSuite = $suite;
                    continue;
                }
            }
            if ($globalSuite == null) {
                $globalSuite = "@base";
            }
        }
        if (strpos($line, "Feature:") !== false) {
            $currentFeature = trim($line);
            continue;
        }

        if ((strpos($line, "Scenario:") !== false || strpos($line, "Scenario Outline:") !== false) && !$isDisabled) {
            $currentSuite = $localSuite ?? $globalSuite;
            $localSuite = null;
            if (is_null($currentSuite)) {
                echo sprintf("Found scenario but currentSuite = %s and currentFeature = %s\n", $currentSuite, $currentFeature);
            }
            if (!isset($scenariosInFeatureAndSuite[$currentSuite][$currentFeature])) {
                $scenariosInFeatureAndSuite[$currentSuite][$currentFeature] = [];
            }
            array_push($scenariosInFeatureAndSuite[$currentSuite][$currentFeature], trim($line));
        }
    }
}

$finalOutput = "<html>";
$finalOutput .= "<link href=\"http://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/readable/bootstrap.min.css\" rel=\"stylesheet\">";
$finalOutput .= "<body>";
$finalOutput .= "<button id=\"collapse_show\">SHOW ALL</button>&nbsp&nbsp&nbsp<button id=\"collapse_hide\">HIDE ALL</button><br><br>";
$anchor = 0;
foreach ($scenariosInFeatureAndSuite as $suiteName => $features) {
    $suiteName = str_replace("@", "", $suiteName);
    $finalOutput .= "<div class=\"panel panel-success\">";
    $finalOutput .= "<div class=\"panel-heading\"><h3 class=\"panel-title\"><a data-toggle=\"collapse\" href=\"#collapse" . $suiteName . "\">" . $suiteName . " suite" . "</h3></div>";
    $finalOutput .= "<div id=\"collapse" . $suiteName . "\" class=\"panel-collapse collapse\">";
    $finalOutput .= "<div class=\"panel-body\">";
    foreach ($features as $featureName => $scenarios) {
        $finalOutput .= "<div class=\"panel panel-primary\">";
        $finalOutput .= "<div class=\"panel-heading\"><h3 class=\"panel-title\"><a data-toggle=\"collapse\" href=\"#collapse" . $anchor . "\">" . $featureName . "</h3></div>";
        $finalOutput .= "<div id=\"collapse" . $anchor++ . "\" class=\"panel-collapse collapse\">";
        $finalOutput .= "<div class=\"panel-body\">";
        foreach ($scenarios as $scenarioName) {
            $finalOutput .= $scenarioName . "<br>";
        }
        $finalOutput .= "</div></div></div>";
    }
    $finalOutput .= "</div></div></div>";
}
$finalOutput .= "<script src=\"http://code.jquery.com/jquery-1.11.0.min.js\"></script>";
$finalOutput .= "<script src=\"http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js\"></script>";
$finalOutput .= "<script>
$(\"#collapse_show\").click(function(e) {
  e.preventDefault();
  $(\".panel-collapse\").collapse(\"show\");
});

$(\"#collapse_hide\").click(function(e) {
  e.preventDefault();
  $(\".panel-collapse\").collapse(\"hide\");
});
</script>";
$finalOutput .= "</body>";
$finalOutput .= "</html>";

file_put_contents($outputFile, $finalOutput);
