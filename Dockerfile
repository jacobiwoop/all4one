FROM node:18-slim

RUN apt-get update && \
    apt-get install -y git \
    && apt-get install -y wget ca-certificates fonts-liberation libappindicator3-1 libasound2 libatk-bridge2.0-0 libatk1.0-0 libcups2 libdbus-1-3 libdrm2 libgbm1 libnspr4 libnss3 libxcomposite1 libxdamage1 libxrandr2 xdg-utils chromium && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/src/app

RUN git clone https://github.com/jacobiwoop/pupeteer.git .

RUN npm install puppeteer@latest --save

CMD ["node", "index.js"]
