<template>
  <div>
    <div class="progress">
      <div class="progress-bar" id="progressBar" role="progressbar" :aria-valuenow="percent" :style="'width: '+percent+'%'" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <table class="table col-8 ml-auto mr-auto">
      <thead>
        <tr>
          <th width="20%" class="text-center">Letter</th>
          <th>Matchings</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in letterMatches">
          <td class="text-center">{{index}}</td>
          <td class="text-center">{{item}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  export default {
    props: ['percent', 'letterMatches']
  }
</script>
