@get_helper_requesters
Feature: Get the list of requesters of one helper
  @loginAsUser2
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
    And the JSON node "hydra:totalItems" should be equal to the number 1
    And the JSON node "hydra:member[0].name" should be equal to the string "foo"

  @loginAsUser1
  @setToken
  Scenario: Gets the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/requesters?order[requester.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

  @setToken
  @secureClient
  Scenario: Add the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a http request to add a set of requesters to my list with body pattern:
    """
      {
        "requesters": [
          {
            "requester": "%user2@test.com%",
            "helperShoppingItems": [
              {
                "requester_shopping_item": "%item_2_1%"
              },
              {
                "requester_shopping_item": "%item_2_2%"
              }
            ]
          },
          {
            "requester": "%user3@test.com%",
            "helperShoppingItems": [
              {
                "requester_shopping_item": "%item_3_1%"
              }
            ]
          }
        ]
      }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"

  @setToken
  Scenario: Gets the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/requesters?order[requester.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 2
    And the JSON node "hydra:member[0].requester.first_name" should be equal to the string "User2"
    And the JSON node "hydra:member[0].helper_shopping_items" should have 2 elements
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.name" should be equal to the string "bar"
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.status" should be equal to the string "pending"
    And the JSON node "hydra:member[0].helper_shopping_items[1].requester_shopping_item.name" should be equal to the string "foo"
    And the JSON node "hydra:member[0].helper_shopping_items[1].requester_shopping_item.status" should be equal to the string "pending"
    And the JSON node "hydra:member[1].requester.first_name" should be equal to the string "User3"
    And the JSON node "hydra:member[1].helper_shopping_items" should have 1 element
    And the JSON node "hydra:member[1].helper_shopping_items[0].requester_shopping_item.name" should be equal to the string "foo"
    And the JSON node "hydra:member[1].helper_shopping_items[0].requester_shopping_item.status" should be equal to the string "pending"

  @loginAsUser2
  @setToken
  Scenario: Gets the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/requesters?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

  @setToken
  Scenario: Gets the list of my helpers
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/helpers?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 1
    And the JSON node "hydra:member[0].helper.first_name" should be equal to the string "User1"
    And the JSON node "hydra:member[0].helper_shopping_items" should have 2 elements
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.name" should be equal to the string "bar"
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.status" should be equal to the string "pending"
    And the JSON node "hydra:member[0].helper_shopping_items[1].requester_shopping_item.name" should be equal to the string "foo"
    And the JSON node "hydra:member[0].helper_shopping_items[1].requester_shopping_item.status" should be equal to the string "pending"

  @loginAsUser3
  @setToken
  Scenario: Gets the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/requesters?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

  @setToken
  Scenario: Gets the list of my helpers
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/helpers?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 1
    And the JSON node "hydra:member[0].helper.first_name" should be equal to the string "User1"
    And the JSON node "hydra:member[0].helper_shopping_items" should have 1 element
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.name" should be equal to the string "foo"
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.status" should be equal to the string "pending"

  @loginAsUser1
  @setToken
  @secureClient
  Scenario: Update the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a http request to add a set of requesters to my list with body pattern:
    """
      {
        "requesters": [
          {
            "requester": "%user2@test.com%",
            "helperShoppingItems": [
              {
                "requester_shopping_item": "%item_2_2%"
              }
            ]
          }
        ]
      }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"

  @setToken
  Scenario: Gets the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/requesters?order[requester.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 1
    And the JSON node "hydra:member[0].requester.first_name" should be equal to the string "User2"
    And the JSON node "hydra:member[0].helper_shopping_items" should have 1 elements
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.name" should be equal to the string "foo"
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.status" should be equal to the string "pending"

  @loginAsUser2
  @setToken
  Scenario: Gets the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/requesters?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

  @setToken
  Scenario: Gets the list of my helpers
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/helpers?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 1
    And the JSON node "hydra:member[0].helper.first_name" should be equal to the string "User1"
    And the JSON node "hydra:member[0].helper_shopping_items" should have 1 elements
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.name" should be equal to the string "foo"
    And the JSON node "hydra:member[0].helper_shopping_items[0].requester_shopping_item.status" should be equal to the string "pending"

  @loginAsUser3
  @setToken
  Scenario: Gets the list of my requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/requesters?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

  @setToken
  Scenario: Gets the list of my helpers
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me/helpers?order[helper.firstName]"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0
