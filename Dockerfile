FROM node:18-slim

# Installer git et les dépendances système pour Chromium
RUN apt-get update && \
    apt-get install -y git \
    && apt-get install -y wget ca-certificates fonts-liberation libappindicator3-1 libasound2 libatk-bridge2.0-0 libatk1.0-0 libcups2 libdbus-1-3 libdrm2 libgbm1 libnspr4 libnss3 libxcomposite1 libxdamage1 libxrandr2 xdg-utils chromium && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/src/app

# Cloner le dépôt
RUN git clone https://github.com/jacobiwoop/pupeteer.git .

# Remplacer puppeteer-core par puppeteer (complet) dans le package.json
RUN npm install puppeteer --save

# Lancer le script
CMD ["node", "index.js"]
