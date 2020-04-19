@get_requester_shopping_items
Feature: Get requester shopping items
  @loginAsUser2
  @setToken
  @secureClient
  Scenario: Create one shopping item
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I create a current shopping item with body:
    """
      {
        "name": "foobar"
      }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "name" should be equal to the string "foobar"

  @setToken
  @secureClient
  Scenario: Create one shopping item
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
  @secureClient
  Scenario: Create one shopping item
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I create a current shopping item with body:
    """
      {
        "name": "bar"
      }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "name" should be equal to the string "bar"

  @setToken
  Scenario: Get all shopping items
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/requester_shopping_items?order[name]=asc"
    Then the response status code should be 405

  @setToken
  Scenario: Get my shopping items
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/shopping_items?order[name]=asc"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 3
    And the JSON node "hydra:member[0].name" should be equal to the string "bar"
    And the JSON node "hydra:member[1].name" should be equal to the string "foo"
    And the JSON node "hydra:member[2].name" should be equal to the string "foobar"

  @loginAsUser3
  @setToken
  Scenario: Get my shopping items
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/shopping_items?order[name]=asc"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0
