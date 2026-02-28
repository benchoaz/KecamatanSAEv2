FROM nginx:alpine

# Copy custom nginx config
COPY conf.d /etc/nginx/conf.d/

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD nginx -t || exit 1

CMD ["nginx", "-g", "daemon off;"]
