# Utilisation d'une image officielle Node.js (qui contient déjà Node.js et npm)
FROM node:18-slim

# Installation de git et des dépendances système nécessaires à puppeteer
RUN apt-get update && \
    apt-get install -y git \
    && apt-get install -y chromium \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Définition du dossier de travail
WORKDIR /usr/src/app

# Clonage du dépôt GitHub
RUN git clone https://github.com/jacobiwoop/pupeteer.git .

# Installation des dépendances Node.js
RUN npm install

# Commande à exécuter au démarrage
CMD ["node", "index.js"]
