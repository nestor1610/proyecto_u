<template>
	<li class="nav-item d-md-down-none">
		<a class="nav-link" href="#" data-toggle="dropdown">
			<i class="icon-bell"></i>
			<span class="badge badge-pill badge-danger">
				{{ notifications.length }}
			</span>
		</a>
		<div class="dropdown-menu dropdown-menu-right">
			<div class="dropdown-header text-center">
				<strong>Notificaciones</strong>
			</div>
			<div v-if="notifications.length">
				<li v-for="item in listar" :key="item.id">	
					<a class="dropdown-item" href="#">
						<i class="fa fa-envelope-o"></i>
						{{ item.ingresos.msj }}
						<span class="badge badge-success">
							{{ item.ingresos.numero }}
						</span>
					</a>
					<a class="dropdown-item" href="#">
						<i class="fa fa-tasks"></i>
						{{ item.ventas.msj }}
						<span class="badge badge-danger">
							{{ item.ventas.numero }}
						</span>
					</a>
				</li>
			</div>
			<div v-else>
				<a><span>No tiene notificaciones</span></a>
			</div>
		</div>
	</li>
</template>
<script>
	export default {
		props : ['notifications'],
		data () {
			return {
				array_notifications : []
			}
		},
		computed : {
			listar : function () {
				//return this.notifications[0];
				this.array_notifications = Object.values( this.notifications[0] );

				if (this.notifications == '') {
					return this.array_notifications = [];
				} else {

					this.array_notifications = Object.values( this.notifications[0] );

					if (this.array_notifications.length > 3)
						return Object.values(this.array_notifications[4]);
					else
						return Object.values(this.array_notifications[0]);

				}
				//return Object.values(this.array_notifications[0]);
			}
		}
	}
</script>