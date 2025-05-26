# Utilise une image Node.js légère
FROM node:18-slim

# Met à jour les paquets et installe les dépendances nécessaires
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
    openssh-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Définis le répertoire de travail
WORKDIR /usr/src/app

# Clone le dépôt Puppeteer
RUN git clone https://github.com/jacobiwoop/pupeteer.git .

# Installe Puppeteer (dernière version)
RUN npm install puppeteer@latest --save

# Expose le port 10000 (optionnel pour Docker)
EXPOSE 3000

# Commande de démarrage : 
# - Crée un tunnel SSH via Serveo (port 80 public vers 10000 local)
# - Lance le serveur Node.js (index.js)
CMD ssh -o StrictHostKeyChecking=no -R 80:localhost:3000 serveo.net & node index.js
