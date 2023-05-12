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
                            <button class="btn btn-primary" type="button" onclick="showAddModal()">{$ADD_IFRAME} <i
                                    class="fa fa-plus-circle">
                                </i></button>
                            <div class="float-md-right">
                                <a style="display:inline" href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
                            </div>
                        </div>
                        <hr>
                        {if count($IFRAME_LIST)}
                            <h4 class="text-center">{$IFRAME}</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        {foreach from=$IFRAME_LIST item=iframe}
                                            <tr>
                                                <td>
                                                    <strong><a href="{$iframe.edit_link}">{$iframe.name}</strong>
                                                </td>
                                                <td>
                                                    <div class="float-md-right">
                                                        <a class="btn btn-warning btn-sm" href="{$iframe.edit_link}"><i
                                                                class="nav-icon fas fa-edit fa-fw"></i></a>
                                                        <button class="btn btn-danger btn-sm" type="button"
                                                            onclick="showDeleteModal('{$iframe.delete_link}')"><i
                                                                class="nav-icon fas fa-trash fa-fw"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            <h4 class="text-center">{$NO_IFRAME}</h4>
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
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{$ADD_IFRAME}</h5>
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
                                    <label>{$DESCRIPTION}</label>
                                    <div class="form-group">
                                        <textarea name="content" class="form-control" id="reply">{$CONTENT}</textarea>
                                    </div>
                                    <label>{$FOOTER_DESCRIPTION}</label>
                                    <div class="form-group">
                                        <textarea name="footer_content" class="form-control"
                                            id="footer_reply">{$FOOTER_CONTENT}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="Src">{$SRC}</label>
                                        <input type="text" id="Src" name="src" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="Iframe_size">{$IFRAME_SIZE}</label>
                                        <select class="form-control mr-sm-2" id="Iframe_size" name="iframe_size">
                                            <option value="21by9">21:9 aspect ratio</option>
                                            <option value="16by9">16:9 aspect ratio</option>
                                            <option value="4by3">4:3 aspect ratio</option>
                                            <option value="1by1">1:1 aspect ratio</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="add" value="add">
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

        function showAddModal() {
            $('#add').attr('href');
            $('#addModal').modal().show();
        }
    </script>
</body>