FROM php:8.3-fpm-alpine

# Install runtime dependencies dan PHP extensions dalam satu layer
RUN apk add --no-cache \
    # Runtime libraries only
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    libxml2 \
    # Build dependencies (temporary)
    && apk add --no-cache --virtual .build-deps \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    libxml2-dev \
    # Install PHP extensions
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql exif pcntl bcmath gd zip \
    # Cleanup build dependencies dan cache
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /usr/src/*

# Set working directory
WORKDIR /var/www/app

# Copy application files (exclude yang tidak perlu via .dockerignore)
COPY . .

# Generate optimized autoloader dan cleanup composer
RUN rm -rf \
    .git \
    .github \
    tests \
    .env.example \
    .editorconfig \
    .gitignore \
    .gitattributes \
    README.md \
    *.md

# Set proper permissions
RUN chown -R www-data:www-data /var/www/app \
    && chmod -R 755 /var/www/app/storage /var/www/app/bootstrap/cache

# Switch to non-root user
USER www-data

# Expose port
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

# Start PHP-FPM
CMD ["php-fpm"]
