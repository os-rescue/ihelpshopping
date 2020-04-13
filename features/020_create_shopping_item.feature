@create_shopping_item
Feature: Create shopping item
  @loginAsUser1
  @setToken
  @secureClient
  Scenario: Create shopping item
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/shopping_items" with body:
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
  @secureClient
  Scenario: I can't create a shopping item with empty name
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/shopping_items" with body:
    """
      {
        "name": ""
      }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | name |
      | violations[0].message | not_blank   |

  @setToken
  @secureClient
  Scenario: I can't create a shopping item with null name
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/shopping_items" with body:
    """
      {
      }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | name |
      | violations[0].message | not_null   |
      | violations[1].propertyPath | name |
      | violations[1].message | not_blank   |
