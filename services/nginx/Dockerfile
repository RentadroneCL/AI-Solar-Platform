FROM nginx:stable-alpine

LABEL maintainer="Alvaro Farias - alvaro.farias@rentadrone.cl"

RUN  rm /etc/nginx/conf.d/default.conf

COPY nginx.conf /etc/nginx
COPY sites/*.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available /etc/nginx/sites-enabled

RUN apk update \
    && apk upgrade \
    && apk --update add logrotate \
    && apk add --no-cache openssl \
    && apk add --no-cache bash \
    && apk add --no-cache curl

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

RUN mkdir /etc/nginx/ssl

RUN openssl genrsa -out "/etc/nginx/ssl/default.key" 2048 \
    && openssl req -new -key "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.csr" -subj "/CN=default/O=default/C=US" \
    && openssl x509 -req -days 365 -in "/etc/nginx/ssl/default.csr" -signkey "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.crt" \
    && chmod 644 /etc/nginx/ssl/default.key

CMD ["nginx", "-g", "daemon off;"]

EXPOSE 80 443
