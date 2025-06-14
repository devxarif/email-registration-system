
#NPM dependencies
FROM node:14 as npm-build
WORKDIR /var/www/html 
COPY package.json package-lock.json vite.config.js /var/www/html/
COPY resources/  /var/www/html/resources/
COPY public/ /var/www/html/public/

RUN npm ci
RUN npm run build


FROM nginx:latest
COPY .docker/nginx/nginx.prod.conf /etc/nginx/conf.d/default.conf

COPY --chown=www-data --from=npm-build /var/www/html/public/ /var/www/html/public/
COPY --chown=www-data . /var/www/html