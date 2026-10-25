FROM node:22-alpine AS frontend

WORKDIR /app

COPY Frontend-Astro/package.json Frontend-Astro/package-lock.json ./
COPY Frontend-Astro/core ./core
RUN npm ci --no-fund --no-audit

COPY Frontend-Astro/ ./

ARG SITE=http://localhost:8080
ARG PUBLIC_API_BASE_URL=/api
ENV SITE=$SITE
ENV PUBLIC_API_BASE_URL=$PUBLIC_API_BASE_URL

RUN npm run build

FROM nginx:stable-alpine

COPY docker/production/web/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=frontend /app/dist /usr/share/nginx/html

# Filament publishes its assets into Laravel's public directory. The web
# image serves the public site from nginx, so they are copied into that root
# instead of being proxied to PHP-FPM.
COPY public/css/filament /usr/share/nginx/html/css/filament
COPY public/js/filament /usr/share/nginx/html/js/filament
COPY public/favicon.ico /usr/share/nginx/html/favicon.ico

EXPOSE 80
