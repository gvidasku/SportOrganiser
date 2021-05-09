<template>
  <div>
    <div class="card p-0 m-0">
      <div class="card-body p-3">
        <div class="d-flex align-items-center small mb-0">
          <i class="fas fa-search mr-1"></i>
          <strong>Detali Paieška</strong>
        </div>
        <a
          href="#"
          class="sportevent-filter d-md-none d-none"
          data-toggle="collapse"
          data-target="#accordion"
          aria-expanded="true"
          aria-controls="accordion"
        >
          <i class="icon icon-list"></i> Filter
        </a>
      </div>
    </div>
    <div id="accordion">
      <div class="card border-top-0">
        <div class="card-body p-3" id="sporteventcity">
          <div class="pb-0">
            <div class="card-title mb-1">Miestas</div>
            <div class="card-body p-0">
              <div class="form-group">
                <select
                  name="sportevent_city"
                  class="form-control"
                  placeholder="Filter by sportevent city"
                  @change="filtercity($event)"
                >
                  <option disabled selected value>
                    -- Pasirinkite --
                  </option>
                  <option
                    v-for="city in city"
                    :value="city.id"
                    :key="city.id"
                  >
                    {{ city.city_name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <hr class="my-3" />
          <div class="pb-0">
            <div class="card-title mb-1">Sporto Rūšis</div>
            <div class="card-body p-0">
              <div class="form-group">
                <select
                  name="sportevent_city"
                  class="form-control"
                  placeholder="Filter by sportevent city"
                  @change="filtersporteventLevel($event)"
                >
                  <option disabled selected value>
                    -- Pasirinkite --
                  </option>
                  <option value="Lengvoji atletika">Lengvoji atletika</option>
                  <option value="Krepšinis">Krepšinis</option>
                  <option value="Futbolas">Futbolas</option>
                  <option value="Asmenin. treniruotė">Asmenin. treniruotė</option>
                  <option value="Grupinės treniruotės">Grupinės treniruotės</option>
                  <option value="Bėgimas">Bėgimas</option>
                  <option value="Tenisas">Tenisas</option>
                  <option value="Stalo tenisas">Stalo tenisas</option>
                  <option value="Plaukimas">Plaukimas</option>
                  <option value="Sporto renginys">Sporto renginys</option>
                    
                </select>
              </div>
            </div>
          </div>
          <hr class="my-3" />
          <div class="pb-0">
            <div class="card-title mb-1">Lygmuo</div>
            <div class="card-body p-0">
              <div class="form-group">
                <select
                  name="sportevent_city"
                  class="form-control"
                  placeholder="Filter by sportevent city"
                  @change="filtersporttype($event)"
                >
                  <option disabled selected value>
                    -- Pasirinkite --
                  </option>
                  <option value="Profesionalus">Profesionalus</option>
                  <option value="Mėgėjiškas">Mėgėjiškas</option>
                </select>
              </div>
            </div>
          </div>
          <hr class="my-3" />
          <div class="pb-0">
            <div class="card-title mb-1">Užsiėmimo tipas</div>
            <div class="card-body p-0">
              <div class="form-group">
                <select
                  name="sportevent_city"
                  class="form-control"
                  placeholder="Filter by sportevent city"
                  @change="filtersportcategoryType($event)"
                >
                  <option disabled selected value>
                    -- Pasirinkite --
                  </option>
                  <option value="Mokamas">Mokamas</option>
                  <option value="Nemokamai">Nemokamai</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "sidebar-component",
  data() {
    return {
      city: [],
    };
  },
  mounted() {
    this.setCategoies();
  },
  methods: {
    setCategoies() {
      axios
        .get("/api/organisator-city")
        .then((res) => res.data)
        .then((data) => {
          this.city = JSON.parse(JSON.stringify(data));
        });
    },
    filtercity(e) {
      this.$emit("get-by-city", e.target.value);
    },
    filtersportcategoryType(e) {
      this.$emit("get-by-sportcategoryType", e.target.value);
    },
    filtersporttype(e) {
      this.$emit("get-by-sporttype", e.target.value);
    },
    filtersporteventLevel(e) {
      this.$emit("get-by-sportevent-level", e.target.value);
    },
  },
};
</script>

<style>
</style>