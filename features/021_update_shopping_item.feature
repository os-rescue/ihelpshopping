@update_shopping_item
Feature: Create shopping item
  @loginAsUser1
  @setToken
  @secureClient
  Scenario: Create the shopping item to be used in the next scenario
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I create a current shopping item with body:
    """
      {
        "name": "foo"
      }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "name" should be equal to the string "foo"

  @setToken
  Scenario: Gets the current shopping item
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I get the current shopping item
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "name" should be equal to the string "foo"

  @setToken
  @secureClient
  Scenario: Update the current shopping item
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I update the current shopping item with body:
    """
      {
        "name": "bar"
      }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "name" should be equal to the string "bar"

  @setToken
  Scenario: Gets the current shopping item
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I get the current shopping item
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "name" should be equal to the string "bar"
