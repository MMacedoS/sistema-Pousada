        <!-- App navbar starts -->
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <div class="offcanvas offcanvas-end" id="MobileMenu">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title semibold">Navegação</h5>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="offcanvas">
                            <i class="icon-clear"></i>
                        </button>
                    </div>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item <?=$active === 'dashboard' ? 'active-link': ''?>">
                            <a class="nav-link " href="/dashboard">
                                <i class="icon-stacked_line_chart"></i> Painel de Controle
                            </a>
                        </li>                   
                        <li class="nav-item dropdown <?=$active === 'reservations' ? 'active-link': ''?>">
                            <? if (hasPermission('visualizar_reservas')): ?>
                                <a class="nav-link dropdown-toggle" href="/dashboard" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-calendar"></i> Reservas
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item current-page" href="/reservas">
                                            <span>Reservar</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item current-page" href="/admin/colaborators">
                                            <span>Consultar</span>
                                        </a>
                                    </li>                                
                                    <li>
                                        <a class="dropdown-item current-page" href="/admin/colaborators">
                                            <span>Mapa</span>
                                        </a>
                                    </li>
                                </ul>
                            <? endif; ?>
                        </li>  
                        <li class="nav-item dropdown <?=$active === 'apartments' ? 'active-link': ''?>">
                            <a href="/apartments" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-briefcase"></i> Apartamentos
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item current-page" href="/apartments">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Apartamentos Ocupados</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown <?=$active === 'sales' ? 'active-link': ''?>">
                            <a href="/apartments" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-style"></i> Vendas
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Apartamentos Ocupados</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown <?=$active === 'payments' ? 'active-link': ''?>">
                            <a href="/apartments" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-dollar-sign"></i> Pagamentos
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Apartamentos Ocupados</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown <?=$active === 'pub' ? 'active-link': ''?>">
                            <a href="/apartments" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-restaurant"></i> Bar
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Apartamentos Ocupados</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown <?=$active === 'reports' ? 'active-link': ''?>">
                            <a href="/apartments" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-activity"></i> Relatorios
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Lista</span>
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item current-page" href="/admin/sectors">
                                        <span>Apartamentos Ocupados</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown <?=$active === 'settings' ? 'active-link': ''?>">
                            <a href="/apartments" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-settings"></i> Configurações
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item current-page" href="/settings">
                                        <span>Parámetros do Sistema</span>
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item current-page" href="/users">
                                        <span>Usuário</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="/logout">
                                <i class="icon-log-out"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- App Navbar ends -->