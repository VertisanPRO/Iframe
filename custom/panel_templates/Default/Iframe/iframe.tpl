{include file='header.tpl'}

<body id="page-top">

	<!-- Wrapper -->
	<div id="wrapper">

		<!-- Sidebar -->
		{include file='sidebar.tpl'}

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">

			<!-- Main content -->
			<div id="content">

				<!-- Topbar -->
				{include file='navbar.tpl'}

				<!-- Begin Page Content -->
				<div class="container-fluid">

					<!-- Page Heading -->
					<div class="d-sm-flex align-items-center justify-content-between mb-4">

						<div class="row mb-2">
							<div class="col-sm-6">
								<h1 class="m-0 text-dark">{$TITLE}</h1>
							</div>
						</div>
					</div>

					<section class="content">
						{if isset($SUCCESS)}
							<div class="alert alert-success alert-dismissible">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h5><i class="icon fa fa-check"></i> {$SUCCESS_TITLE}</h5>
								{$SUCCESS}
							</div>
						{/if}

						{if isset($ERRORS) && count($ERRORS)}
							<div class="alert alert-danger alert-dismissible">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h5><i class="icon fas fa-exclamation-triangle"></i> {$ERRORS_TITLE}</h5>
								<ul>
									{foreach from=$ERRORS item=error}
										<li>{$error}</li>
									{/foreach}
								</ul>
							</div>
						{/if}


						<div class="float-md">
							<button class="btn btn-primary" type="button" onclick="showAddModal()">{$ADD_PAGE}
								</i></button>
						</div>
						<hr>
						{if count($PAGES_LIST)}
							<h4 class="text-center">{$PAGES}</h4>
							<div class="table-responsive">
								<table class="table table-striped">
									<tbody>
										{foreach from=$PAGES_LIST item=page}
											<tr>
												<td>
													<strong><a href="{$page.setting_link}">{$page.name}</strong>
												</td>
												<td>
													<div class="float-md-right">
														<a class="btn btn-primary btn-sm" href="{$page.setting_link}"><i
																class="nav-icon fas fa-sliders-h fa-fw"></i></a>
														<a class="btn btn-warning btn-sm" href="{$page.edit_link}"><i
																class="nav-icon fas fa-edit fa-fw"></i></a>
														<button class="btn btn-danger btn-sm" type="button"
															onclick="showDeleteModal('{$page.delete_link}')"><i
																class="nav-icon fas fa-trash fa-fw"></i></button>
													</div>
												</td>
											</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
						{else}
							<h4 class="text-center">{$NO_PAGES}</h4>
						{/if}

					</section>

				</div>


				<!-- Modal Form -->

				<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">{$ARE_YOU_SURE}</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								{$CONFIRM_DELETE}
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
								<a href="#" id="delete" class="btn btn-primary">{$YES}</a>
							</div>
						</div>
					</div>
				</div>


				<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">{$ADD_PAGE}</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<form action="" method="post">
									<div class="form-group">
										<label for="Name">{$NAME}</label>
										<input type="text" id="Name" name="name" class="form-control">
									</div>

									<div class="form-group">
										<label for="Url">{$URL}</label>
										<input type="text" id="Url" name="url" class="form-control">
									</div>

									<div class="form-group">
										<input type="hidden" name="token" value="{$TOKEN}">
										<input type="submit" class="btn btn-primary" value="{$SUBMIT}">
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

			</div>

			{include file='footer.tpl'}


		</div>
	</div>
	<!-- ./wrapper -->


	{include file='scripts.tpl'}

	<script type="text/javascript">
		function showDeleteModal(id) {
			$('#delete').attr('href', id);
			$('#deleteModal').modal().show();
		}
	</script>

	<script type="text/javascript">
		function showAddModal() {
			$('#add').attr('href');
			$('#addModal').modal().show();
		}
	</script>

</body>

</html>