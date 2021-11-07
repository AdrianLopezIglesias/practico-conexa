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
					b-nav-item Nuevo local
					b-nav-item-dropdown(right="")
						// Using 'button-content' slot
						template(slot="button-content")
							em Usuario
						b-dropdown-item(href="#") Profile
						b-dropdown-item(href="#") Sign Out
	b-modal#modal-1(title="Nuevo elemento")
		input(type="text" class="form-control" placeholder="nombre de la tarea")
		input(type="text" class="form-control" placeholder="nombre de la tarea")
</template>

<script>
export default {
  name: "HelloWorld",
  data() {
    return {
      msg: "Welcome to Your Vue.js App",
			show: true
    };
  },
	methods: {
	}
};
</script>

