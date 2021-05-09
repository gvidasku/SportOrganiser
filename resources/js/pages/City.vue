<template>
  <div class="sportevent-cateogory">
    <div class="card border shadow mb-5">
      <div class="card-header">Užsiėmimai pagal miestą</div>
      <div class="card-body">
        <div class="row">
          <div
            class="col-sm-12 col-md-4"
            v-for="city in city"
            :key="city.id"
          >
            <div class="card-list-component">
              <a
                :href="`/Organisator/${city.id}-${city.city_name}`"
                target="_blank"
              >
                {{ city.city_name }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "sportevent-city",
  data() {
    return {
      city: [],
    };
  },
  created() {
    this.$Progress.start();
    axios
      .get("/api/organisator-city")
      .then((res) => res.data)
      .then((data) => {
        this.city = data;
        this.$Progress.finish();
      })
      .catch((err) => {
        console.log(err);
        this.$Progress.fail();
      });
  },
};
</script>

<style>
</style>