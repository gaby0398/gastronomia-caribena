import { Routes } from '@angular/router';
import { IndexComponent } from './components/index/index.component';
import { GestionUsuariosComponent } from './components/gestion-usuarios/gestion-usuarios.component';
import { InformacionComponent } from './components/informacion/informacion.component';
import { LoginComponent } from './components/login/login.component';
import { ComidasComponent } from './components/comidas/comidas.component';
import { CrearcomidasComponent } from './components/crearcomidas/crearcomidas.component';
import { ActualizarcomidasComponent } from './components/actualizarcomidas/actualizarcomidas.component';
import { ComidaEspecificaComponent } from './components/comida-especifica/comida-especifica.component';
import { PlantasComponent } from './components/plantas/plantas.component';
import { CrearplantasComponent } from './components/crearplantas/crearplantas.component';
import { PlantaEspecificaComponent } from './components/planta-especifica/planta-especifica.component';
import { RestaurantesComponent } from './components/restaurantes/restaurantes.component';

export const routes: Routes = [
    { path: '', component: IndexComponent },
    { path: 'gestion-usuarios', component: GestionUsuariosComponent },
    { path: 'informacion', component: InformacionComponent },
    { path: 'login', component: LoginComponent },
    {path: 'comidas', component : ComidasComponent},
    {path: 'crearComida', component: CrearcomidasComponent},
    {path: 'actualizarComida', component: ActualizarcomidasComponent},
    {path: 'comidaEspecifica', component: ComidaEspecificaComponent },
    {path: 'plantas', component: PlantasComponent },
    {path: 'crearplantas', component: CrearplantasComponent},
    {path: 'plantaEspecifica', component: PlantaEspecificaComponent},
    {path: 'restaurantes', component: RestaurantesComponent} 
    // Ruta por defecto que apunta a IndexComponent
];
