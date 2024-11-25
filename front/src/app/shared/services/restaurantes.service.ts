import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class RestauranteService {
  private baseUrl = 'http://localhost:9000/api/restaurantes'; // ruta de donde consumo la API DE LA VARA 

  constructor(private http: HttpClient) { }


 // TODOS LOS SERVICIOS SON APLICABLES PARA LOS DEMÁS MODULOS SOLO ES CAMBIARLE EL NOMBRE DEL MODULO ESPECIFICO ARRIBA :)



  // Método para obtener todas las comidas (read general)
 getRestaurantes(): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/read`);
  }


  // Método para filtrar restaurante
  filtrarRestaurantes(nombre: string): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/filtro/${nombre}`);
  }

  // CREATE DE restaurante
  crearRestaurante(restaurante: any): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}`, restaurante);
  }

  
 
   // read con id especifico
   getRestauranteId(id: number): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/read/${id}`);  // URL con el ID
  }

  

  updateRestaurante(id: number, restaurante: any): Observable<any> {
    return this.http.put<any>(`${this.baseUrl}/${id}`, restaurante);  // URL con el ID
  }





}
