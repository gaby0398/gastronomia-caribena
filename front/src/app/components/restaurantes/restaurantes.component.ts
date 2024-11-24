import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router,RouterModule } from '@angular/router';
import { RestauranteService } from '../../shared/services/restaurantes.service';
 
@Component({
  selector: 'app-restaurantes',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './restaurantes.component.html',
  styleUrls: ['./restaurantes.component.css']
})
export class RestaurantesComponent implements OnInit{
  titulo: string = '';  // para modificar input del html 

  publicaciones: any[] = [];   
  publicacionesFiltradas: any[] = [];


  textoBoton: string = 'Buscar'; // buscar - para restablecer


  ultimaBusqueda: string = ''; // Guarda el valor de la última búsqueda
 
  paginaActual: number = 1; // Página actual
  publicacionesPorPagina: number = 5; // Cantidad de publicaciones por página
  //private ManejaIntervalo: any;  // Esto aún tiende a dar error OJO
 
  constructor(private RestauranteService:RestauranteService, private router: Router ) {}


  ngOnInit(): void {
    this.getrestaurantes(); // Carga inicial de todas las publicaciones
    //  this.ManejaIntervalo = setInterval(() => {
  //    this.getComidas();  //Hilo que refresca las comidas que existen de la bd.
   // }, 5000);  // Me estaba dando un error medio extraño.
  }

  getrestaurantes(): void {
    this.RestauranteService.getRestaurantes().subscribe(
      (resp) => {
        if (resp && resp.data) {
          this.mapearPublicaciones(resp.data);
          this.resetBoton(); // Restablece el botón a "Buscar"
          this.actualizarPaginacion(); // Divide en páginas
        }
      },
      (error) => {
        console.error('Error al obtener las comidas:', error);
      }
    );
  }



  mapearPublicaciones(data: any[]): void {
    this.publicaciones = data.map((restaurante: any) => ({
      fecha: restaurante.fecha_creacion,
      usuario: `@UsuarioID${restaurante.usuario_id}`,
      nombre: restaurante.nombre_restaurante,
      descripcion_planta: restaurante.descripcion_restaurante,
      imagenUrl: restaurante.imagen,
      idComida : restaurante.id_restaurante
    }));
  }

  
  get totalPaginas(): number[] {
    return Array(Math.ceil(this.publicaciones.length / this.publicacionesPorPagina));
  }


  actualizarPaginacion(): void {
    const inicio = (this.paginaActual - 1) * this.publicacionesPorPagina;
    const fin = inicio + this.publicacionesPorPagina;
    this.publicacionesFiltradas = this.publicaciones.slice(inicio, fin);
  }

  cambiarPagina(pagina: number): void {
    this.paginaActual = pagina;
    this.actualizarPaginacion();
  }

  resetBoton(): void {
    this.textoBoton = 'Buscar';
    this.ultimaBusqueda = '';
  }

  cambiaTexto(): void {
    this.textoBoton = 'Restablecer';
  }

  onInputChange(): void {
    if (this.titulo !== this.ultimaBusqueda) {
      this.textoBoton = 'Buscar';
    }
  }



  filtrarRestaurantes(): void {
    if (this.titulo.trim() === '') {
      this.cambiaTexto();
      this.getrestaurantes();
    } else if (this.titulo === this.ultimaBusqueda) {
      this.cambiaTexto();
      this.getrestaurantes();
    } else {
      this.RestauranteService.filtrarRestaurantes(this.titulo).subscribe(
        (resp) => {
          if (resp && resp.data) {
            this.mapearPublicaciones(resp.data);
            this.ultimaBusqueda = this.titulo;
            this.textoBoton = 'Restablecer'; // Cambiar el texto del botón
            this.actualizarPaginacion(); // Divide en páginas
          } else {
            this.publicaciones = [];
            console.warn('No hay datos disponibles para el filtro.');
            this.cambiaTexto();
            this.titulo = '';
          }
        },
        (error) => {
          console.error('Error al filtrar las comidas:', error);
        }
      );
    }
  }


  onSearch(): void {
    this.filtrarRestaurantes();
  }


  
  goBack(): void {
    this.router.navigate(['/']);
  }
   

  haciaPublicacionEspecifica(event: MouseEvent,id: number) : void{
    localStorage.setItem("idRestaurante", id.toString()); 
        
    this.router.navigate(['/restauranteEspecifica']); //PARA QUE ESTO FUNCIONE SE DEBE PONER EN EL CONSTRUCTOR JUNTO AL SERVICIO.
   
  }


  // tenes que cambiar el nombre de 
  // los dos primeros manejadores de eventos evitan errores medios raros de propagación. En definitiva NO TOCARLOS.
  EditarPublicacion(event: MouseEvent,id: number): void {
    event.stopPropagation();  // Esto previene que el <a> se active
    event.preventDefault(); // este al parecer funciona super bien en conjunto con el anterior. Para evitar la caida de la pagina

   
    const IDComida = (id); // Almaceno id de publicacion especifica por parametro
    localStorage.setItem("idRestaurante", id.toString()); 
    //alert('Editar publicación con ID: ' + localStorage.getItem("idRestaurante"));
    
    this.router.navigate(['/actualizarRestaurante']); //PARA QUE ESTO FUNCIONE SE DEBE PONER EN EL CONSTRUCTOR JUNTO AL SERVICIO.
  
  }
  

 

























}


