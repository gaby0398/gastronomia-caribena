import { Routes } from '@angular/router';
import { IndexComponent } from './components/index/index.component';
import { GestionUsuariosComponent } from './components/gestion-usuarios/gestion-usuarios.component';
import { InformacionComponent } from './components/informacion/informacion.component';
import { LoginComponent } from './components/login/login.component';

export const routes: Routes = [
    { path: '', component: IndexComponent },
    { path: 'gestion-usuarios', component: GestionUsuariosComponent },
    { path: 'informacion', component: InformacionComponent },
    { path: 'login', component: LoginComponent }
      // Ruta por defecto que apunta a IndexComponent
];
