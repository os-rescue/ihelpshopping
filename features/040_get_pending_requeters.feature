@get_pending_requesters
Feature: Get pending requesters
  @loginAsUser1
  @setToken
  Scenario: Gets pending requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users?account_type=requester&nb_pending_items[gt]=0"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

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
    And the JSON node "nb_pending_items" should be equal to the number 0

  @setToken
  Scenario: Gets pending requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users?account_type=requester&nb_pending_items[gt]=0"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

  @setToken
  @secureClient
  Scenario: Create one shopping item
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

  @setToken
  Scenario: Gets my profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "nb_pending_items" should be equal to the number 1

  @setToken
  Scenario: Gets pending requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users?account_type=requester&nb_pending_items[gt]=0"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 1
    And the JSON node "hydra:member[0].first_name" should be equal to the string "User1"

  @loginAsUser2
  @setToken
  Scenario: Gets pending requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users?account_type=requester&nb_pending_items[gt]=0"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 1
    And the JSON node "hydra:member[0].first_name" should be equal to the string "User1"
