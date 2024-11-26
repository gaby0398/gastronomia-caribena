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

import { ActualizarplantasComponent } from './components/actualizarplantas/actualizarplantas.component';

import { RestauranteEspecificaComponent } from './components/restaurante-especifica/restaurante-especifica.component';
import { ActualizarRestaurantesComponent } from './components/actualizar-restaurantes/actualizar-restaurantes.component';
import { CrearRestaurantesComponent } from './components/crearestaurantes/crearestaurantes.component';

import { CrearCuentaComponent } from './components/crear-cuenta/crear-cuenta.component';
import { RemarkComponent } from './components/remark/remark.component';


export const routes: Routes = [
    { path: '', component: IndexComponent },
    { path: 'gestion-usuarios', component: GestionUsuariosComponent },
    { path: 'info', component: InformacionComponent },
    { path: 'login', component: LoginComponent },
    {path: 'comidas', component : ComidasComponent},
    {path: 'crearComida', component: CrearcomidasComponent},
    {path: 'actualizarComida', component: ActualizarcomidasComponent},
    {path: 'comidaEspecifica', component: ComidaEspecificaComponent },
    {path: 'plantas', component: PlantasComponent },
    {path: 'crearplanta', component: CrearplantasComponent},
    {path: 'plantaEspecifica', component: PlantaEspecificaComponent},

    {path: 'actulizarPlanta', component: ActualizarplantasComponent},
    {path: 'restaurantes', component: RestaurantesComponent},
    {path: 'crearrestaurante', component: CrearRestaurantesComponent},
    {path: 'restauranteEspecifica', component:RestauranteEspecificaComponent },
    {path: 'actualizarRestaurante', component: ActualizarRestaurantesComponent},

    {path: 'restaurantes', component: RestaurantesComponent},
    {path: 'crear-cuenta', component: CrearCuentaComponent},

    {path: 'comentarios', component: RemarkComponent}
    // Ruta por defecto que apunta a IndexComponent
];