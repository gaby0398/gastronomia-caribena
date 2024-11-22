import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';



@Injectable({
  providedIn: 'root'
})
export class PlantasService {

  private baseUrl = 'http://localhost:9000/api/plantas'; // ruta de donde consumo la API ESPECIFICA de plantas

  constructor(private http: HttpClient) { }


// TODOS LOS SERVICIOS SON APLICABLES PARA LOS DEMÁS MODULOS SOLO ES CAMBIARLE EL NOMBRE DEL MODULO ESPECIFICO ARRIBA :)



  // Método para obtener todas las comidas (read general)
  getPlantas(): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/read`);
  }


  // Método para filtrar comidas
  filtrarPlantas(nombre: string): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/filtro/${nombre}`);
  }

  // CREATE DE COMIDAS
  crearPlanta(planta: any): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}`, planta);
  }

  
 
   // read con id especifico
   getPlantaId(id: number): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/read/${id}`);  // URL con el ID
  }

  

  updatePlanta(id: number, planta: any): Observable<any> {
    return this.http.put<any>(`${this.baseUrl}/${id}`, planta);  // URL con el ID
  }











}
