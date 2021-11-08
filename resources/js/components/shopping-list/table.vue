<template lang="pug">
div
	b-navbar(toggleable="lg" type="dark" variant="success")
		b-container
			b-navbar-brand(href="#") Shop-Keeper
			b-navbar-toggle(target="nav-collapse")
			b-collapse#nav-collapse(is-nav="")
				// Right aligned nav items
				b-navbar-nav.ml-auto
					b-nav-item(v-b-modal.modal-1) Nuevo elemento
					b-nav-item(v-b-modal.modal-2) Nuevo local
					b-nav-item(@click="subirTareas()") Subir tareas
					b-nav-item(@click="descargarTareas()") Descargar tareas
	b-modal#modal-1(title="Nuevo elemento" @ok="handleOkItem")
		br
		input(type="text" name="text" v-model="new_text" class="form-control" placeholder="Nombre del elemento")
		br
		b-form-select(v-model="new_local" :options="locales.filter(x=> {return x.activo == 1})" value-field="id") 
		br
		br
		b-form-select(v-model="new_value" :options="cantidades") 
		br
	b-modal#modal-2(title="Nuevo local" @ok="handleOkLocal")
		br
		input(type="text" name="text" v-model="new_local_text" class="form-control" placeholder="Nombre del local")
		br

	.container-fluid
		div
			br
			table.table
				tbody
					tr
						td(style={ width: '20%' }) Local 
						td 
							select.form-control(
								placeholder="Local"
								v-model="filter_local",
						  	@change="filter"
						  )
								option(value="") Todos
								option(v-for="(l, index) in locales.filter(x=> {return x.activo == 1})", 
									v-bind:key="l.id" 
									v-bind:value="l.id"
									)  {{ l.text }}


					tr
						td(style={ width: '20%' }) Cantidad 
						td 
							select.form-control(
								v-model="filter_estado",
						  	@change="filter"
						  )
								option(value="" selected) Todos
								option(value="pendientes") Pendientes
			br
			table.table
				thead
					tr
						th text 
						th Cantidad
						th Local
				tbody
					tr(v-for="(e, index) in elementos_filtrados", :key="index") 
						td(v-on:click="eliminar(e.id)") {{ e.text }} 
						td
							b-form-select(v-model="e.cantidad" :options="cantidades" @change="actualizarElemento($event, e.id)") 
						td(v-on:click="eliminar_local(e.local_id)") {{(locales.filter((x) => x.id == e.local_id))[0].text }}
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      shopKeeper: {},
      elementos: [],
      elementos_filtrados: [],
      locales: [],
      filter_local: "",
      filter_estado: "",
      cantidades: [0, 1, 2, 3, 4, 5, 10, 15, 20, 30, 60],
      new_value: "",
      new_local: "",
      new_text: "",
      new_local_text: "",
    };
  },
  mounted() {
    this.startShopKeeper();
    this.filter();
  },
  methods: {
    localesActivos() {
      return this.locales.filter((x) => {
        return x.activo == 1;
      });
    },
    filter() {
      this.elementos_filtrados = this.elementos;
      if (this.filter_local) {
        this.elementos_filtrados = this.elementos_filtrados.filter((x) => {
          return x.local_id == this.filter_local;
        });
      }
      if (this.filter_estado == "pendientes") {
        this.elementos_filtrados = this.elementos_filtrados.filter((x) => {
          return x.cantidad != 0;
        });
      }
      this.elementos_filtrados = this.elementos_filtrados.filter((x) => {
        return x.activo == 1;
      });
    },
    eliminar(id) {
      if (confirm("Seguro que desea eliminar tarea " + id)) {
        this.del(id);
      } else {
      }
    },
    eliminar_local(id) {
      if (confirm("Seguro que desea eliminar el local " + id)) {
        this.del_loc(id);
      } else {
      }
    },
    del_loc(id) {
      let index = this.locales.findIndex((x) => x.id == id);
      if (index > -1) {
        this.locales[index].activo = 0;
        this.locales[index].updated_at = new Date().getTime();
      }
      let eliminar_elementos = this.elementos.filter((x) => {
        return x.local_id == id;
      });
      console.log(eliminar_elementos);

      eliminar_elementos.forEach((x) => {
        this.del(x.id);
      });
      this.shopKeeper.elementos = this.elementos;
      localStorage.setItem("shop-keeper", JSON.stringify(this.shopKeeper));
      this.filter();
    },
    del(id) {
      let index = this.elementos.findIndex((x) => x.id == id);
      if (index > -1) {
        this.elementos[index].activo = 0;
        this.elementos[index].updated_at = new Date().getTime();
      }
      this.shopKeeper.elementos = this.elementos;
      localStorage.setItem("shop-keeper", JSON.stringify(this.shopKeeper));
      this.filter();
    },
    startShopKeeper() {
      this.shopKeeper = JSON.parse(localStorage.getItem("shop-keeper"));
      if (!this.shopKeeper) {
        this.newShopKeeper();
      }
      this.elementos = this.shopKeeper.elementos;
      this.elementos_filtrados = this.elementos;
      this.locales = this.shopKeeper.locales;
    },
    newShopKeeper() {
      this.shopKeeper = {
        elementos: [
          {
            id: 1,
            text: "acero",
            local_id: 1,
            cantidad: 1,
            activo: 1,
            updated_at: new Date().getTime(),
          },
          {
            id: 2,
            text: "madera",
            local_id: 2,
            cantidad: 1,
            activo: 1,
            updated_at: new Date().getTime(),
          },
          {
            id: 3,
            text: "clavos",
            local_id: 2,
            cantidad: 10,
            activo: 1,
            updated_at: new Date().getTime(),
          },
          {
            id: 4,
            text: "palos",
            local_id: 2,
            cantidad: 0,
            activo: 1,
            updated_at: new Date().getTime(),
          },
        ],
        locales: [
          { text: "metal-shop", id: "1", activo: 1, updated_at: new Date().getTime() },
          { text: "wood-shop", id: "2", activo: 1, updated_at: new Date().getTime() },
        ],
      };
      localStorage.setItem("shop-keeper", JSON.stringify(this.shopKeeper));
    },
    actualizarElemento(new_value, element_id) {
      if (element_id == 0) {
        element_id = Math.round(Math.random() * 100000);
        this.elementos.push({
          id: element_id,
          text: this.new_text,
          local_id: this.new_local,
          cantidad: new_value,
          activo: 1,
          updated_at: new Date().getTime(),
        });
      } else {
        let elemento = this.elementos.findIndex((x) => x.id == element_id);
        this.elementos[elemento].cantidad = new_value;
        this.elementos[elemento].updated_at = new Date().getTime();
      }
      this.shopKeeper.elementos = this.elementos;
      localStorage.setItem("shop-keeper", JSON.stringify(this.shopKeeper));
      this.filter();
      this.new_text = "";
      this.new_local = "";
      this.new_value = "";
    },
    descargarTareas() {
      if (confirm("Seguro que desea actualizar las tareas locales?")) {
        let url = "/api/tareas";
        let data = {
          shopKeeper: this.shopKeeper,
          accion: "bajar",
        };
        return axios.post(url, data).then((x) => {
          console.log(x.data);
          this.shopKeeper.elementos = x.data.elementos;
          this.elementos = this.shopKeeper.elementos;
          console.log(
            "🚀 ~ file: table.vue ~ line 241 ~ returnaxios.post ~ this.shopKeeper.elementos",
            this.shopKeeper.elementos
          );
          this.shopKeeper.locales = x.data.locales;
          this.locales = this.shopKeeper.locales;
          console.log(
            "🚀 ~ file: table.vue ~ line 243 ~ returnaxios.post ~ this.shopKeeper.locales",
            this.shopKeeper.locales
          );
          localStorage.setItem("shop-keeper", JSON.stringify(this.shopKeeper));
          this.filter();
        });
      } else {
      }
    },
    subirTareas() {
      if (confirm("Seguro que desea actualizar las tareas en la DB?")) {
        let url = "/api/tareas";
        let data = {
          shopKeeper: this.shopKeeper,
          accion: "subir",
        };
        return axios.post(url, data).then((x) => {
          console.log("ok");
          console.log(x);
        });
      }
    },
    handleOkLocal(bvModalEvt) {
      bvModalEvt.preventDefault();
      this.newLocal();
    },
    newLocal() {
      let new_local = {
        id: Math.round(Math.random() * 100000),
        text: this.new_local_text,
        activo: 1,
        updated_at: new Date().getTime(),
      };
      this.locales.push(new_local);
      this.shopKeeper.locales = this.locales;
      localStorage.setItem("shop-keeper", JSON.stringify(this.shopKeeper));
      this.new_local_text = "";
    },
    handleOkItem(bvModalEvt) {
      bvModalEvt.preventDefault();
      console.log(bvModalEvt);
      this.handleSubmit();
    },
    handleSubmit() {
      this.actualizarElemento(this.new_value, 0);
      this.$nextTick(() => {
        this.$bvModal.hide("modal-prevent-closing");
      });
    },
  },
};
</script>
