# PHP Selenium Framework for Automated Tests

Prerequisites:
  
- PHP 7(or higher) and composer installed  
- PHP extensions: php_curl and php_mbstring enabled in php.ini  (optional - troubleshooting)
- Chrome in version 61.0 (can be other but may be incompatible with selenium version)

Setup:
  
1. Clone the above repository  
2. Run composer update  
3. Download latest chromedriver and add it to PATH
4. In one tab start selenium server provided in the repostitory(3.6.0): "java -jar filename.jar"  

Run tests:
  
In order to run the tests simply do (in repository root):
 
    "vendor/bin/behat -v -c src/config/behat.yml -s core"
    
Static code analysis locally:

    "vendor/bin/phpstan analyse -l 7 -c phpstan_settings.neon src/"
    
I recommend using php-cs-fixer (manually or as pre-commit hook):

    "vendor/bin/php-cs-fixer fix src/"