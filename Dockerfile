FROM node:18-slim

# Installer les dépendances nécessaires
RUN apt-get update && \
    apt-get install -y \
    git \
    wget \
    ca-certificates \
    fonts-liberation \
    libappindicator3-1 \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libcups2 \
    libdbus-1-3 \
    libdrm2 \
    libgbm1 \
    libnspr4 \
    libnss3 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    xdg-utils \
    chromium \
    --no-install-recommends && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/src/app

# Cloner le dépôt
RUN git clone https://github.com/jacobiwoop/pupeteer.git .

# Installer Puppeteer avec la dernière version
RUN npm install puppeteer@latest --save

# S'assurer que l'application écoute sur 0.0.0.0
# (ATTENTION : vérifie que ton index.js utilise bien 0.0.0.0)
EXPOSE 10000

CMD ["node", "index.js"]
