@sign_up
Feature: signing up
  @secureClient
  Scenario: Try to create a user with missing mandatory fields
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
      {}
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | first_name   |
      | violations[0].message | not_null   |
      | violations[1].propertyPath | first_name   |
      | violations[1].message | not_blank   |
      | violations[2].propertyPath | last_name   |
      | violations[2].message | not_null   |
      | violations[3].propertyPath | last_name   |
      | violations[3].message | not_blank   |
      | violations[6].propertyPath | email   |
      | violations[6].message | not_null   |
      | violations[7].propertyPath | email   |
      | violations[7].message | not_blank   |
      | violations[8].propertyPath | username   |
      | violations[8].message | not_null   |
      | violations[9].propertyPath | username   |
      | violations[9].message | not_blank   |

  @secureClient
  Scenario: Creates a valid external employee user
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
      {
        "email": "user4@test.com",
        "first_name": "foo",
        "last_name": "bar"
      }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should be equal to:
      | email | user4@test.com   |
      | first_name | foo   |
      | last_name | bar   |

  @secureClient
  Scenario: Attempt to create a user with a duplicated email
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
      {
        "email": "user4@test.com",
        "first_name": "bar",
        "last_name": "foo"
      }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | email_canonical |
      | violations[0].message | already.exist   |

  @secureClient
  Scenario: Authenticate with the previous credentials
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/login_check" with body:
    """
      {
        "email": "user4@test.com",
        "password": "AAAbbb111#"
      }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "token" should not be null
    And the JSON node "data" should have 1 element
    And the JSON node "data.user_id" should not be null
    And the JSON node "refresh_token" should not be null
