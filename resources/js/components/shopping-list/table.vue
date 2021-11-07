<template lang="pug">
div
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
								option(v-for="(l, index) in locales", 
									v-bind:key="l.id" 
									v-bind:value="l.id"
									)  {{ l.nombre }}


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
						th Nombre 
						th Cantidad
						th Local
				tbody
					tr(v-for="(e, index) in elementos_filtrados", :key="index") 
						td {{ e.nombre }}
						td
							b-form-select(v-model="e.cantidad" :options="cantidades" @change="actualizarElemento($event, e.id)") 
						td {{e.local_id}}
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
			cantidades: [1,2,3,4,5,10,15,20,30,60]
    };
  },
  mounted() {
		this.startShopKeeper()
  },
  methods: {
		filter(){
			this.elementos_filtrados = this.elementos; 
			if(this.filter_local){
				this.elementos_filtrados = this.elementos_filtrados.filter(x => {
					return x.local_id == this.filter_local
				})
			}
			if(this.filter_estado == 'pendientes'){
				this.elementos_filtrados = this.elementos_filtrados.filter(x => {
					return x.cantidad != 0
				})
			}
		},
		startShopKeeper(){
			this.shopKeeper = JSON.parse(localStorage.getItem('shop-keeper'));
			if(!this.shopKeeper){
				this.newShopKeeper()
			}
			this.elementos = this.shopKeeper.elementos
			this.elementos_filtrados = this.elementos
			this.locales = this.shopKeeper.locales
			this.syncShopKeeper();
		},
		newShopKeeper(){
			this.shopKeeper = {
				elementos: [
					{id: 1, nombre: "acero", local_id: 1, cantidad: 1, activo: 1, updated_at: new Date().getTime()},
					{id: 2, nombre: "madera", local_id: 2, cantidad: 1, activo: 1, updated_at: new Date().getTime()},
					{id: 3, nombre: "clavos", local_id: 2, cantidad: 10, activo: 1, updated_at: new Date().getTime()},  
					{id: 4, nombre: "palos", local_id: 2, cantidad: 0, activo: 1, updated_at: new Date().getTime()}  
				],
				locales: [
					{nombre: 'metal-shop', id: '1', updated_at: new Date().getTime()},
					{nombre: 'wood-shop', id: '2', updated_at: new Date().getTime()}
					]
			}
			localStorage.setItem('shop-keeper', JSON.stringify(this.shopKeeper));
		},
		actualizarElemento(new_value, element_id){
			let elemento = this.elementos.findIndex((x => x.id == element_id));
			this.elementos[elemento].cantidad = new_value;
			this.shopKeeper.elementos = this.elementos; 
			localStorage.setItem('shop-keeper', JSON.stringify(this.shopKeeper));
			this.filter()
		},
    syncShopKeeper() {
			// let url =  "/api/pacientes" 
			// url += "?nombre=" + this.paciente_search_nombre
			// url += "&dni=" + this.paciente_search_dni
			// url += "&telefono=" + this.paciente_search_telefono
			// console.log(url)
      // return axios
      //   .get(url)
      //   .then((x) => {
			// 		console.log(x.data.data.data)
      //     this.pacientes = x.data.data.data;
      //   });
    },
  },
};
</script>
