Before you raise the server:
1. Install the required libraries using the command: <code>composer install</code>
2. Configure the database connection in the .env file

<code>DATABASE_URL="mysql://{user}:{password}@127.0.0.1:3306/{dbname}?serverVersion=5.7"</code>

3. Import migrations <code>php bin/console doctrine:migrations:migrate

The server is started with the command: <code>php bin/console server:run</code>

After that you can open it locally at <link>http://127.0.0.1:8000</link>


Test data from the admin panel:
Login: admin@example.com
Password: 12345
