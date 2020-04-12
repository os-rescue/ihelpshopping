@get_users
Feature: Get Users
  @loginAsUser1
  @setToken
  Scenario: Gets my profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/me"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON should be valid according to this schema:
     """
     {
       "$schema": "http://json-schema.org/draft-07/schema",
       "type": "object",
       "title": "The Root Schema",
       "required": [
         "@context",
         "@id",
         "@type",
         "email",
         "first_name",
         "last_name",
         "middle_name",
         "address",
         "title",
         "phone_number",
         "mobile_number",
         "account_type",
         "admin"
       ],
       "properties": {
         "@context": {
           "$id": "#/properties/@context",
           "title": "The @context Schema",
           "type": "string",
           "examples": [
             "/api/contexts/User"
           ]
         },
         "@id": {
           "$id": "#/properties/@id",
           "title": "The @id Schema",
           "type": "string",
           "examples": [
             "/api/users/7e1fc5ea-7b9b-4gf8-8777-evv4c199f4f6"
           ]
         },
         "@type": {
           "$id": "#/properties/@type",
           "title": "The @type Schema",
           "type": "string",
           "examples": [
             "User"
           ]
         },
         "email": {
           "$id": "#/properties/email",
           "title": "The email Schema",
           "type": "string",
           "examples": [
             "foo@bar.com"
           ]
         },
         "enabled": {
           "$id": "#/properties/enabled",
           "title": "The enabled Schema",
           "type": "boolean",
           "examples": [
             "true"
           ]
         },
         "locked": {
           "$id": "#/properties/locked",
           "title": "The locked Schema",
           "type": "boolean",
           "examples": [
             "true"
           ]
         },
         "first_name": {
           "$id": "#/properties/first_name",
           "title": "The first_name Schema",
           "type": "string",
           "examples": [
             "foo"
           ]
         },
         "last_name": {
           "$id": "#/properties/last_name",
           "title": "The last_name Schema",
           "type": "string",
           "examples": [
             "bar"
           ]
         },
         "middle_name": {
           "$id": "#/properties/middle_name",
           "title": "The middle_name Schema",
           "type": ["string", "null"],
           "examples": [
             "foobar"
           ]
         },
         "address": {
           "$id": "#/properties/address",
           "title": "The address Schema",
           "type": "string",
           "examples": [
             "test address"
           ]
         },
         "title": {
           "$id": "#/properties/title",
           "title": "The title Schema",
           "type": ["null", "string"],
           "examples": [
             "Mr."
           ]
         },
         "phone_number": {
           "$id": "#/properties/phone_number",
           "title": "The phone_number Schema",
           "type": ["string", "null"],
           "examples": [
             "+1234567890"
           ]
         },
         "mobile_number": {
           "$id": "#/properties/mobile_number",
           "title": "The mobile_number Schema",
           "type": ["string", "null"],
           "examples": [
             "+0987654321"
           ]
         },
         "account_type": {
           "$id": "#/properties/account_type",
           "title": "The Account_type Schema",
           "type": ["string", "null"],
           "enum": [null, "requester", "helper"],
           "examples": [
             "requester"
           ]
         },
         "admin": {
           "$id": "#/properties/admin",
           "title": "The admin Schema",
           "type": "boolean",
           "examples": [
             "false"
           ]
         },
         "last_login": {
           "$id": "#/properties/last_login",
           "title": "The last_login Schema",
           "type": ["string", "null"],
           "examples": [
             "2020-02-18T10:50:41+00:00"
           ]
         }
       },
       "additionalProperties": false
     }
     """

  Scenario: Gets user data via token
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/get-data-by-token/foobar"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON nodes should be equal to:
      | first_name | User3  |
      | middle_name | Foo  |
      | last_name | Test3  |
    And the JSON should be valid according to this schema:
     """
     {
       "type": "object",
       "$schema": "http://json-schema.org/draft-07/schema",
       "required": [
         "email",
         "first_name",
         "last_name",
         "middle_name",
         "admin"
       ],
       "properties": {
         "email": {
           "$id": "#/properties/email",
           "title": "The email Schema",
           "type": "string",
           "examples": [
             "foo@bar.com"
           ]
         },
         "first_name": {
           "$id": "#/properties/first_name",
           "title": "The first_name Schema",
           "type": "string",
           "examples": [
             "foo"
           ]
         },
         "last_name": {
           "$id": "#/properties/last_name",
           "title": "The last_name Schema",
           "type": "string",
           "examples": [
             "bar"
           ]
         },
         "middle_name": {
           "$id": "#/properties/middle_name",
           "title": "The middle_name Schema",
           "type": ["string", "null"],
           "examples": [
             "foobar"
           ]
         },
         "admin": {
           "$id": "#/properties/admin",
           "title": "The admin Schema",
           "type": "boolean",
           "examples": [
             "false"
           ]
         }
       },
       "additionalProperties": false
     }
     """

  Scenario: Gets user data via token
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "/api/users/get-data-by-token/invalid"
    Then the response status code should be 404
