dev:
	# Copy environmental variables
	cp .env.example .env
	# Build containers and start servers
	docker-compose up -d --build
	# Install dependencies for api and consumers
	docker exec api composer install
	docker exec consumer-1 composer install
	# Wait 1 minute before load data so MySQL server finishes the initialization
	sleep 60
	# Create database structure and load some data
	docker exec -i database mysql -u user -ppassword -D quotes < dump.sql

consume:
	# Start consumers to actually consume messages
	# Use -d option in order to the commands execute in the background
	docker exec consumer-1 php src/consume.php -d
	docker exec consumer-2 php src/consume.php -d

run-unit-tests:
	# Run unit tests on api and consumer projects
	docker exec api vendor/phpunit/phpunit/phpunit tests/UnitTests --configuration=phpunit.xml
	docker exec consumer-1 vendor/phpunit/phpunit/phpunit tests/UnitTests --configuration=phpunit.xml

run-integration-tests:
	# Run integration tests on api project
	docker exec api vendor/phpunit/phpunit/phpunit tests/IntegrationTests --configuration=phpunit.xml