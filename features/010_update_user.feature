@update_user
Feature: Update user data
  @loginAsUser1
  @setToken
  Scenario: Gets my profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "account_type" should be null

  @setToken
  @secureClient
  Scenario: Gets my profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/users/me" with body:
    """
      {
        "account_type": "requester"
      }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "account_type" should be equal to the string "requester"

  @setToken
  @secureClient
  Scenario: Gets my profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/users/me" with body:
    """
      {
        "account_type": "helper"
      }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "account_type" should be equal to the string "helper"

  @setToken
  @secureClient
  Scenario: Gets my profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "PUT" request to "/api/users/me" with body:
    """
      {
        "account_type": "invalid"
      }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | account_type |
      | violations[0].message | invalid   |
