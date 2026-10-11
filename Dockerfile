FROM php:8.2-apache

# Cài đặt extension PDO MySQL cần thiết
RUN docker-php-ext-install pdo pdo_mysql

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Cấu hình Apache cho phép .htaccess ghi đè (AllowOverride All)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Cấu hình Apache lắng nghe theo PORT động của Render
ENV PORT=80
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Copy toàn bộ mã nguồn vào thư mục web của Apache
WORKDIR /var/www/html
COPY . /var/www/html/

# Phân quyền ghi cho thư mục uploads và public/uploads
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
