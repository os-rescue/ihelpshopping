@get_shopping_items
Feature: Get shopping items
  @loginAsUser1
  @setToken
  @secureClient
  Scenario: Create the shopping item to be used in the next scenario
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
  @secureClient
  Scenario: Create the shopping item to be used in the next scenario
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
  Scenario: Get my shopping items
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/shopping_items?order[name]=asc"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 3
    And the JSON node "hydra:member[0].name" should be equal to the string "bar"
    And the JSON node "hydra:member[1].name" should be equal to the string "foo"
    And the JSON node "hydra:member[2].name" should be equal to the string "foobar"

  @loginAsUser2
  @setToken
  Scenario: Get my shopping items
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/shopping_items?order[name]=asc"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0
