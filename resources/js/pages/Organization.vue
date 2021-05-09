<template>
  <div class="organization">
    <div class="card border shadow mb-5">
      <div class="card-header">Užsiėmimai pagal Organizatorių</div>
      <div class="card-body">
        <div class="row">
          <div
            class="col-sm-12 col-md-4"
            v-for="organisator in organisators"
            :key="organisator.id"
          >
            <div class="card-list-component">
              <a
                :href="`/Organisator/${organisator.id}-${organisator.title}`"
                target="_blank"
              >
                {{ organisator.title }}
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
      organisators: [],
    };
  },
  created() {
    this.$Progress.start();
    axios
      .get("/api/organisators")
      .then((res) => res.data)
      .then((data) => {
        this.organisators = data;
        this.$Progress.finish();
      })
      .catch((err) => {
        console.log(err);
        this.$Progress.fail();
      });
  },
};
</script>

