<template>
<div>
  <div class="container">
    <div>
      <br>
      <h1>Space travel</h1>
      <br>
    </div>
    <div class="row">
      <div class="col-6">

        <div class="card"  :key="gameState">
          <div class="card-header text-center">
            <h2>Instructions from earth</h2>
          </div>
          <div class="card-body" :key="shipLocation[3]">
            <div v-if="gameState!='loadingMovements'">
              <movement v-for="(movement,index) in movements" v-bind:key="index" v-bind:index="index" v-bind:movement="movement" :current_action="current_action">
              </movement>
              <div class="row">
                <div class="col-6">
                  <button class="w-100 btn btn-warning" :disabled="userMustWait()" v-on:click="getMovements">
                    <h5>Call for new instructions</h5>
                  </button>

                </div>
                <div class="col-6">
                  <button class="w-100 btn btn-success" :disabled="userMustWait()" v-on:click="followInstructions">
                    <h5>Go with current instructions</h5>
                  </button>

                </div>
              </div>
            </div>

            <div v-if="gameState=='loadingMovements'">
              <img class="w-100" src="/load.gif">
            </div>
          </div>
        </div>

      </div>
      <div class="col-6">

        <div class="card">
          <div class="card-header  text-center">
            <h2>Our location</h2>
          </div>
          <div class="card-body">
            <div class="row" :key="shipLocation[3]" :id="shipLocation[3]">
              <space v-for="(slots,index) in map" v-bind:key="index" v-bind:slots="slots" v-bind:shipLocation="shipLocation">
              </space>
							<div v-if="this.gameState == 'loadingActions'">
              <p class="m-4">Loading Actions from server ...</p>
							</div>
							<div v-if="this.gameState != 'loadingActions'">
              <p class="m-4">Current action: {{shipLocation[2]}}</p>
							</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>
</template>

<script>
var movement = Vue.component('movement', {
  props: ['movement', 'current_action', 'index'],
  template: '<h5 :class="slotStyle">{{movement}}</h5>',
  data() {
    return {
      slotStyle: 'alert alert-primary text-center'
    }
  },
  mounted() {

    if (this.current_action === this.index) {
      this.slotStyle = 'alert alert-warning text-center';
    } else {
      this.slotStyle = 'alert alert-success text-center';
    }
  },
})

var space = Vue.component('space', {
  data() {
    return {
      slotStyle: 'space-empty',
      spaceShip: ''
    }
  },
  props: ['index', 'slots', 'shipLocation'],

  mounted() {
    if (this.shipLocation[0] == this.slots[0] && this.shipLocation[1] == this.slots[1]) {
      this.slotStyle = 'space-ship';
      this.spaceShip = '<i class="fas fa-rocket"></i>'
    } else {
      this.slotStyle = 'space-empty'
      this.spaceShip = ''
    }
  },

  template: `<div :class="slotStyle"> <div class=" text-center w-100  p-2" style="color: white;" > <div v-html="spaceShip"></div></div></div>`
})

import {
  mapActions,
  mapGetters
} from 'vuex';
import {
  getMovements,
  followOrders
} from '../services/MovementService.js';
import {
  library
} from '@fortawesome/fontawesome-svg-core'
import {
  faUserSecret
} from '@fortawesome/free-solid-svg-icons'
import {
  FontAwesomeIcon
} from '@fortawesome/vue-fontawesome'

const timer = ms => new Promise(res => setTimeout(res, ms))

export default {
  data() {
    return {
      movements: [],
      map: [
        [0, 0],
        [0, 1],
        [0, 2],
        [1, 0],
        [1, 1],
        [1, 2],
        [2, 0],
        [2, 1],
        [2, 2]
      ],
      shipLocation: [0, 0, 'Awaiting orders', 'id'],
      gameState: "playing",
      current_action: ' ',
      game_speed: 1000
    }
  },

  mounted() {
    setTimeout(() => {
      this.getMovements();
    }, 200);
    this.generateMap();
  },

  methods: {
    userMustWait() {
      return this.gameState != 'playing';

    },
    generateMap() {
      this.map = [];
      for (let i = 0; i < 10; i++) {
        for (let h = 0; h < 10; h++) {
          this.map.push([i, h])
        }
      }
    },

    async getMovements() {
			console.log("getting movements")
      this.gameState = "loadingMovements";
      setTimeout(async () => {
				getMovements().then(r => {
				this.movements = r.data
      	this.gameState = "playing";
				})
      }, 500);
    },

    async followInstructions() {

      this.gameState = "loadingActions";

			setTimeout(async () => {
				let actions = {
					itmes: this.movements,
					initial: this.shipLocation
				}
				followOrders(actions).then(r => {

					this.actions = r.data

					this.gameState = "moving";

					this.actions.forEach(async (a, index) => {
						this.current_action = index - 1;
						setTimeout(() => {
							this.shipLocation = a;
							this.current_action = index - 1;
						}, index * this.game_speed);
					})

					setTimeout(() => {
						this.getMovements();
						this.gameState = "playing";
						this.current_action = ' ';
						this.shipLocation[2] = "Awaiting orders";
						this.shipLocation[3] = "Finished";
					}, this.actions.length * this.game_speed);

				})
      }, 500);
    }
  }
}
</script>
