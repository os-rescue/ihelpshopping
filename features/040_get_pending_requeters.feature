@get_pending_requesters
Feature: Get pending requesters
  @loginAsUser1
  @setToken
  Scenario: Gets pending requesters
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users?account_type=requester&pending=true"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "hydra:totalItems" should be equal to the number 0

