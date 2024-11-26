import { Component, OnInit } from '@angular/core';
import { CommonModule} from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule, RouterOutlet } from '@angular/router';
import { PlantasService } from '../../shared/services/plantas.service'; 


@Component({
  selector: 'app-plantas',
  standalone: true,
  imports: [CommonModule, FormsModule,RouterOutlet, RouterModule],
  templateUrl: './plantas.component.html',
  styleUrl: './plantas.component.css'
})
export class PlantasComponent implements OnInit {

  titulo: string = '';  // para modificar input del html 

  publicaciones: any[] = [];   
  publicacionesFiltradas: any[] = [];


  textoBoton: string = 'Buscar'; // buscar - para restablecer


  ultimaBusqueda: string = ''; // Guarda el valor de la última búsqueda
 
  paginaActual: number = 1; // Página actual
  publicacionesPorPagina: number = 5; // Cantidad de publicaciones por página
  //private ManejaIntervalo: any;  // Esto aún tiende a dar error OJO

  constructor(private plantasService: PlantasService, private router: Router) {}

  ngOnInit(): void {
    this.getPlantas(); // Carga inicial de todas las publicaciones
    //  this.ManejaIntervalo = setInterval(() => {
  //    this.getComidas();  //Hilo que refresca las comidas que existen de la bd.
   // }, 5000);  // Me estaba dando un error medio extraño.
  }


  getPlantas(): void {
    this.plantasService.getPlantas().subscribe(
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
    this.publicaciones = data.map((planta: any) => ({
      fecha: planta.fecha_creacion,
      usuario: `@UsuarioID${planta.usuario_id}`,
      nombre: planta.nombre_planta,
      descripcion_planta: planta.caracteristicas,
      imagenUrl: planta.imagen,
      idComida : planta.id_planta
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
 

  filtrarPlantas(): void {
    if (this.titulo.trim() === '') {
      this.cambiaTexto();
      this.getPlantas();
    } else if (this.titulo === this.ultimaBusqueda) {
      this.cambiaTexto();
      this.getPlantas();
    } else {
      this.plantasService.filtrarPlantas(this.titulo).subscribe(
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
    this.filtrarPlantas();
  }
 


  goBack(): void {
    this.router.navigate(['/']);
  }
   
   

  haciaPublicacionEspecifica(event: MouseEvent,idComida: number) : void{
    localStorage.setItem("idPlanta", idComida.toString()); 
    //alert('Editar publicación con ID: ' + localStorage.getItem("idPlanta"));
    
    this.router.navigate(['/plantaEspecifica']); //PARA QUE ESTO FUNCIONE SE DEBE PONER EN EL CONSTRUCTOR JUNTO AL SERVICIO.
   
  }


  // tenes que cambiar el nombre de 
  // los dos primeros manejadores de eventos evitan errores medios raros de propagación. En definitiva NO TOCARLOS.
  EditarPublicacion(event: MouseEvent,id: number): void {
    event.stopPropagation();  // Esto previene que el <a> se active
    event.preventDefault(); // este al parecer funciona super bien en conjunto con el anterior. Para evitar la caida de la pagina

   
    const IDComida = (id); // Almaceno id de publicacion especifica por parametro
    localStorage.setItem("idPlanta", id.toString()); 
    //alert('Editar publicación con ID: ' + localStorage.getItem("idPlanta"));
    
    this.router.navigate(['/actulizarPlanta']); //PARA QUE ESTO FUNCIONE SE DEBE PONER EN EL CONSTRUCTOR JUNTO AL SERVICIO.
  
  }
  
}