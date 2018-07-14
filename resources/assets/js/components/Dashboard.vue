<template>
	<main class="main">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/">Escritorio</a>
			</li>
		</ol>
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="card card-chart">
								<div class="card-header">
									<h4>Ingresos</h4>
								</div>
								<div class="card-content">
									<div class="ct-chart">
										<canvas id="ingresos">
										</canvas>
									</div>
								</div>
								<div class="card-footer">
									<p>Compras de los últimos meses.</p>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card card-chart">
								<div class="card-header">
									<h4>Ventas</h4>
								</div>
								<div class="card-content">
									<div class="ct-chart">
										<canvas id="ventas">
										</canvas>
									</div>
								</div>
								<div class="card-footer">
									<p>Ventas de los últimos meses.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</template>
<script>
	export default {
		data () {
			return {
				var_ingreso : null,
				char_ingreso : null,
				ingresos : [],
				var_total_ingreso : [],
				var_mes_ingreso : [],
				var_venta : null,
				char_venta : null,
				ventas : [],
				var_total_venta : [],
				var_mes_venta : [],
			}
		},
		methods : {
			getIngresosVentas () {
				let me = this;
				var url = '/dashboard';

				axios.get(url).then(function (response) {
					me.ingresos = response.data.ingresos;
					me.ventas = response.data.ventas;
					me.loadIngresos();
					me.loadVentas();
				})
				.catch(function (error) {
					console.log(error);
				})
			},
			loadIngresos () {
				let me = this;

				me.ingresos.map(function (x) {
					me.var_mes_ingreso.push(x.mes);
					me.var_total_ingreso.push(x.total);
				});

				me.var_ingreso = document.getElementById('ingresos').getContext('2d');

				me.char_ingreso = new Chart(me.var_ingreso, {
					type: 'bar',
					data: {
						labels: me.var_mes_ingreso,
						datasets: [{
							label: 'Ingresos',
							data: me.var_total_ingreso,
							backgroundColor: 'rgba(255, 99, 132, 0.2)',
							borderColor: 'rgba(255, 99, 132, 0.2)',
							borderWidth: 1
						}]
					},
					options: {
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero:true
								}
							}]
						}
					}
				});
			},
			loadVentas () {
				let me = this;

				me.ventas.map(function (x) {
					me.var_mes_venta.push(x.mes);
					me.var_total_venta.push(x.total);
				});

				me.var_venta = document.getElementById('ventas').getContext('2d');

				me.char_venta = new Chart(me.var_venta, {
					type: 'bar',
					data: {
						labels: me.var_mes_venta,
						datasets: [{
							label: 'Ventas',
							data: me.var_total_venta,
							backgroundColor: 'rgba(54, 162, 235, 0.2)',
							borderColor: 'rgba(54, 162, 235, 0.2)',
							borderWidth: 1
						}]
					},
					options: {
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero:true
								}
							}]
						}
					}
				});
			}
		},
		mounted () {
			this.getIngresosVentas();
		}
	}
</script>