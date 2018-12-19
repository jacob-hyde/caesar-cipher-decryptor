<template>
    <div>
        <div class="form-group">
          <label for="key">Encryption Key</label>
          <textarea class="form-control" id="key" v-model="key" rows="5"></textarea>
        </div>
        <div class="form-group">
          <label for="encrypted">Encrypted Text</label>
          <textarea class="form-control" id="encrypted" v-model="encrypted" rows="5"></textarea>
        </div>
        <div class="form-group text-right">
          <button class="btn btn-primary" v-on:click="decrypt" id="Decrypt">Decrypt</button>
        </div>
        <div class="form-group" v-if="!decrypting">
          <label for="decrypted">Decrypted Text</label>
          <textarea class="form-control" id="decrypted" v-model="decrypted" rows="10" readonly></textarea>
        </div>
        <div v-if="decrypting">
          <while-decrypting v-bind="{percent, letterMatches}"></while-decrypting>
        </div>
      </div>
</template>

<script>
  Vue.component('while-decrypting', require('./WhileDecrypting.vue'));
  import axios from 'axios';
  export default {
    mounted() {
      let self = this;
      Echo.channel('decoding')
        .listen('.decodingUpdatePercent', (data) => {
          self.percent = data.percent;
        })
        .listen('.decodingLetterMatches', (data) => {
          self.letterMatches = data.letterMatches;
        })
    },
    data() {
      return {
        key: '',
        encrypted: '',
        decrypted: '',
        decrypting: false,
        percent: 0,
        letterMatches: {}
      };
    },
    methods: {
      createPostData() {
        return {
          key: this.key,
          encrypted: this.encrypted
        };
      },
      decrypt(e) {
        e.preventDefault();
        this.decrypting = true;
        let self = this;
        axios.post('/api/decrypt', this.createPostData())
        .then(response => {
          self.decrypting = false;
          self.decrypted = response.data;
        })
        .catch(e => {
          self.decrypting = false;
        })
      }
    }
  }
</script>
