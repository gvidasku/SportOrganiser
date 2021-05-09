<template>
  <div class="organization">
    <div class="card border shadow mb-5">
      <div class="card-header">Užsiėmimai pagal sporto rūšį</div>
      <div class="card-body">
        <div class="row">
          <div
            class="col-sm-12 col-md-4"
            v-for="(index, sportevent) in sportevents"
            :key="sportevent.id"
          >
            <div class="card-list-component">
              <a :href="`/sportevent/${index}-${sportevent}`" target="_blank">
                {{ sportevent }}
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
  name: "organization",
  data() {
    return {
      sportevents: [],
    };
  },
  created() {
    this.$Progress.start();
    axios
      .get("/api/sportevent-titles")
      .then((res) => res.data)
      .then((data) => {
        this.sportevents = JSON.parse(JSON.stringify(data));
        this.$Progress.finish();
      })
      .catch((err) => {
        console.log(err);
        this.$Progress.fail();
      });
  },
};
</script>

