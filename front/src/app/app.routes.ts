import { Routes } from '@angular/router';
import { IndexComponent } from './components/index/index.component';
import { GestionUsuariosComponent } from './components/gestion-usuarios/gestion-usuarios.component';

export const routes: Routes = [
    { path: '', component: IndexComponent },
    { path: 'gestion-usuarios', component: GestionUsuariosComponent }
      // Ruta por defecto que apunta a IndexComponent
];
