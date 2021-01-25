FROM node:12-alpine3.11

# Bundle app source
COPY app /business_rules/

# Create app directory
WORKDIR /business_rules/app

RUN npm install
# If you are building your code for production
# RUN npm ci --only=production

EXPOSE 3000
CMD [ "npm", "start" ]
