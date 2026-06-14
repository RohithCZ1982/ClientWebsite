#!/bin/bash

echo "🚀 Starting TechCorp WordPress with Docker..."

# Start containers
docker compose up -d

echo "⏳ Waiting for WordPress to be ready..."
sleep 15

# Check if WordPress is accessible
for i in {1..30}; do
  if curl -s http://localhost > /dev/null 2>&1; then
    echo "✅ WordPress is ready!"
    break
  fi
  echo "  Attempt $i/30 - WordPress not ready yet..."
  sleep 2
done

# Get WordPress container ID
WP_CONTAINER=$(docker compose ps -q wordpress)

# Install WordPress core (wp-cli)
echo "📦 Installing WordPress CLI and setting up WordPress..."
docker exec $WP_CONTAINER sh -c 'cd /var/www/html && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && php wp-cli.phar core install --url=http://localhost --title="TechCorp Solutions" --admin_user=admin --admin_password=admin123 --admin_email=admin@techcorp.local --skip-email --allow-root 2>/dev/null || true'

# Activate TechCorp theme
echo "🎨 Activating TechCorp theme..."
docker exec $WP_CONTAINER sh -c 'cd /var/www/html && php wp-cli.phar theme activate techcorp --allow-root 2>&1'

# Install and activate required plugins
echo "📚 Installing required plugins..."
docker exec $WP_CONTAINER sh -c 'cd /var/www/html && php wp-cli.phar plugin install contact-form-7 --activate --allow-root 2>&1'

echo ""
echo "✨ Setup Complete!"
echo ""
echo "📍 WordPress URL: http://localhost"
echo "👤 Admin Username: admin"
echo "🔑 Admin Password: admin123"
echo ""
echo "📊 MySQL Details:"
echo "   Host: localhost:3306"
echo "   Database: wordpress"
echo "   User: wordpress"
echo "   Password: wordpress"
echo ""
echo "🛑 To stop the containers: docker compose down"
echo "🔄 To restart: docker compose up -d"
