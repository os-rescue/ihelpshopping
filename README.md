# Edu network API Endpoints

This service contains the REST APIs endpoint for Approval Manager product.
In the our platform the name of the service is approval-manager-api.

## Running Local Environment
1. ```git clone git@gitlab.intra:eqs-approval-manager/approval-manager-api.git```
2. ```cd approval-manager-api```
3. ```cp .env .env.local```
4. ```docker-compose up -d```

## Running Linting
Run ```make linting```

## Running PHPUnit Tests
Run ```make phpunit-tests```

## Running Behat Tests
Run ```make behat-tests```

## Swagger
#####Staging  ```https://approval-manager-stage.eqs.intra/api/swagger```
#####QA       ```https://approval-manager-qa.eqs.intra/api/swagger```

### Authentication API endpoint

```
Endpoint: ${HOST}/api/login_check
Method: POST
Body: {"username": "%username%", "password": "%password%"}
```

## Portainer

To check the status of the service after its deployment, you could fetch it by its name per environment on:
https://portainer.eqs.intra

## Kibana
#####Staging https://kibana-stage.eqs.intra
#####QA      https://kibana-qa.eqs.intra
#####Prod    https://kibana-prod.eqs.intra

## NewRelic
https://login.newrelic.com/