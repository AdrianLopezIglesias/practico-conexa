<template>
  <div id="app">
		<NavBar/>
    <ShoppingListTable/>
  </div>
</template>

<script>
import ShoppingListTable from "./components/shopping-list/table"
import NavBar from "./components/ui/NavBar"


const app = {
	name: "App",
	components: { 
		ShoppingListTable,
		NavBar },
};


export default app; 

</script>

