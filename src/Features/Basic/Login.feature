@core @login
Feature: Login
  As a platform user I want to be able to login

  Scenario: Successful login with valid credentials
    Given I am on signin page
     When I enter correct credentials and submit
     Then I should be successfully logged in as this user

  Scenario: Invalid credentials login attempt failure
    Given I am on signin page
     When I enter correct user email and submit
     Then I should not be able to log in
     When I enter incorrect password and submit
     Then I should see Invalid credentials error