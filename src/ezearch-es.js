const { Client } = require('@elastic/elasticsearch')
const client = new Client({
  node: 'http://88.198.32.151:9200'
});
console.log(client)