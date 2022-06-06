up:
	@docker-compose up -d --build
down:
	@docker-compose down
composer-install:
	@docker-compose exec app composer install
key:
	@docker-compose exec app php artisan key:generate
config-cache:
	@docker-compose exec app php artisan config:cache
migrate:
	@docker-compose exec app php artisan migrate
migrate-seed:
	@docker-compose exec app php artisan migrate --seed
bot-register:
	@docker-compose exec app php artisan botman:telegram:register