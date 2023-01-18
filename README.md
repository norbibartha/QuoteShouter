# Project Description

This is a simple REST API, written in Vanilla PHP that returns shouted quotes by famous people. 

Simple quote:


    If life were predictable it would cease to be life, and be without flavor.


Shouted quote:


    IF LIFE WERE PREDICTABLE IT WOULD CEASE TO BE LIFE, AND BE WITHOUT FLAVOR!


The application has two different data sources, MySQL as main database and
a json file (it should have been a 3rd-party API but for simplicity we used a sample).
There is implemented a caching system, which consists from Redis, RabbitMQ and two consumers subscribed to the same queue.
Each entry in the cache is valid for 12 hours.

## Project installation

1. Clone this repository

    ```
    git clone git@github.com:norbibartha/QuoteShouter.git
    ```

2. Install docker and docker-compose

    ```
    sudo apt-get update
    sudo apt install apt-transport-https ca-certificates curl software-properties-common
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
    sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu focal stable"
    sudo apt install docker-ce
    sudo usermod -aG docker ${USER}
    sudo apt install docker-compose
    ```

3. Install Make package

    ```
    sudo apt install make
    ```

4. Generate local development environment

    ```
   cd QuoteShouter
   make dev
   ```

## Debugging

At this point you have the working development environment, the API is accessible at:

```
localhost:8080
```

If you want to check what is going on in RabbitMq, you can do it by accessing the following address from you browser:

```
http://localhost:15672
```

This is the RabbitMq management interface.


## Useful commands:

#### Run unit tests:

```
make run-unit-tests
```

#### Run integration tests:     
```
make run-integration-tests
```

#### Start consumers to actually consume the messages from RabbitMq queue:

```
make consume
```

# Project Requirements

We want you to implement a REST API that, given a famous person and a count N, returns N quotes from this famous person _shouted_ .

Shouting a quote consists of transforming it to uppercase and adding an exclamation mark at the end.

Our application could have multiple sources to get the quotes from, for example an REST API like https://theysaidso.com/api/ could be used,
although for the sake of the test we provided a sample of quotes by famous persons that can be used, so no need to perform real calls to our source API

We also want to get a cache layer of these quotes in order to avoid hitting our source (which let's imagine is very expensive) twice for the same person given a T time.

You must use MySQL as main DB storage, and Redis for the caching layer. 

Caching of items will be done through RabbitMQ. Instead of directly writing to Redis, send a message to RabbitMQ, and have at least 2 workers reading from the queue and storing the items to redis. 

You must build a local environment using docker & docker-compose that can be started with the command: “make dev”. This should start the environment and have it 100% ready and operational, without any manual action needed. 

Build 2 different layers of testing, unit and integration. It’ll be important to have high test coverage.

## Example

Given these quotes from Steve Jobs:
- "The only way to do great work is to love what you do.",
- "Your time is limited, so don’t waste it living someone else’s life!"

The returned response should be:
```
curl -s http://awesomequotesapi.com/shout/steve-jobs?limit=2
[
    "THE ONLY WAY TO DO GREAT WORK IS TO LOVE WHAT YOU DO!",
    "YOUR TIME IS LIMITED, SO DON’T WASTE IT LIVING SOMEONE ELSE’S LIFE!"
]
```

## Constraints
- Count N provided MUST be equal or less than 10. If not, our API should return an error.

