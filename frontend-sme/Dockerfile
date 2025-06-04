FROM node:18

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

# เปิดพอร์ต React dev server
EXPOSE 3000

# Run แบบ development server
CMD ["npm", "start"]
