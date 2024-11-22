import { inject, Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { catchError, map, Observable, of, retry, switchMap, tap, throwError } from 'rxjs';
import { IPassw, Role, TypeClient, TypeClientV2, TypeUser } from '../models/interface';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})

export class ManagerUserService {
  private readonly http = inject(HttpClient);
  private readonly srvAuth = inject(AuthService);
  private readonly SERVER_URL = environment.servidor;
  private readonly ENDPOINT = environment.servidor + '/api/usr';
  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'Application/json'
    })
  };

  getManagerUser(userParam: any): Observable<TypeUser> {
    return this.http.get<any>(`${this.SERVER_URL}/api/usr/getUser/${userParam}`);
  }

  getUser(userParam: any): Observable<TypeClientV2> {
    return this.getManagerUser(userParam).pipe(
      switchMap((user) => {
        let rolePath: string;
        switch (user.rol) {
          case Role.administrador:
            rolePath = 'administrador';
            break;
          case Role.supervisor:
            rolePath = 'supervisor';
            break;
          case Role.cliente:
            rolePath = 'cliente';
            break;
          default:
            throw new Error('Rol desconocido');
        }

        return this.http.get<any>(`${this.SERVER_URL}/api/${rolePath}/read/${user.id}`).pipe(
          map((userData) => ({
            ...userData["0"], // Incluir los datos obtenidos de la llamada
            rol: user.rol // Añadir el rol del usuario
          })),
          tap((data) => {
            console.log('Datos del usuario obtenidos:', data);
          }), // Aquí se imprimen los datos en la consola
          retry(1),
          catchError(this.handleError)
        );
      }),
      tap({
        error: (err) => console.error('Error en getUser:', err)
      }) // Opcional: también puedes registrar errores globalmente aquí
    );
  }

  getAllUsers(userParam: any): Observable<TypeClient[]> {
    return this.getManagerUser(userParam).pipe(
      switchMap((user) => {
        let rolePath: string;
        switch (user.rol) {
          case Role.administrador:
            rolePath = 'administrador';
            break;
          case Role.supervisor:
            rolePath = 'supervisor';
            break;
          case Role.cliente:
            rolePath = 'cliente';
            break;
          default:
            throw new Error('Rol desconocido');
        }

        return this.http.get<any>(`${this.SERVER_URL}/api/${rolePath}/read`).pipe(
          retry(1),
          catchError(this.handleError)
        );
      })
    );
  }

  filterUser(userParam: any, parametros: any): Observable<any> {
    return this.getManagerUser(userParam).pipe(
      switchMap((user) => {
        let rolePath: string;
        switch (user.rol) {
          case Role.administrador:
            rolePath = 'administrador';
            break;
          case Role.supervisor:
            rolePath = 'supervisor';
            break;
          case Role.cliente:
            rolePath = 'cliente';
            break;
          default:
            throw new Error('Rol desconocido');
        }

        let params = new HttpParams();
        for (const prop in parametros) {
          if (prop) {
            params = params.append(prop, parametros[prop]);
          }
        }

        return this.http.get<any>(`${this.SERVER_URL}/api/${rolePath}/filtro`, { params }).pipe(
          retry(1),
          catchError(this.handleError)
        );
      })
    );
  }

  // Función para crear un usuario
  createUser(datos: TypeClient, role: number): Observable<any> {
    let rolePath: string;
    switch (role) {
      case Role.administrador:
        rolePath = 'administrador';
        break;
      case Role.supervisor:
        rolePath = 'supervisor';
        break;
      case Role.cliente:
        rolePath = 'cliente';
        break;
      default:
        throw new Error('Rol desconocido');
    }

    return this.http.post<any>(`${this.SERVER_URL}/api/${rolePath}`, datos).pipe(
      retry(1),
      catchError(this.handleError)
    );
  }

  // Función para actualizar un usuario existente
  updateUser(userParam: any, datos: TypeClient): Observable<any> {
    return this.getManagerUser(userParam).pipe(
      switchMap((user) => {
        let rolePath: string;
        switch (user.rol) {
          case Role.administrador:
            rolePath = 'administrador';
            break;
          case Role.supervisor:
            rolePath = 'supervisor';
            break;
          case Role.cliente:
            rolePath = 'cliente';
            break;
          default:
            throw new Error('Rol desconocido');
        }

        return this.http.put<any>(`${this.SERVER_URL}/api/${rolePath}/${user.id}`, datos).pipe(
          retry(1),
          catchError(this.handleError)
        );
      })
    );
  }

  DeleteUser(userParam: any): Observable<boolean> {
    return this.getManagerUser(userParam).pipe(
      switchMap((user) => {
        // Determinar el segmento dinámico según el rol
        let rolePath: string;
        switch (user.rol) {
          case Role.administrador:
            rolePath = 'administrador';
            break;
          case Role.supervisor:
            rolePath = 'supervisor';
            break;
          case Role.cliente:
            rolePath = 'cliente';
            break;
          default:
            throw new Error('Rol desconocido');
        }

        // Realizar la solicitud DELETE con la ruta dinámica
        return this.http.delete<any>(`${this.SERVER_URL}/api/${rolePath}/${user.id}`).pipe(
          retry(1),
          map(() => true),
          catchError(this.handleError)
        );
      })
    );
  }

  changeRol(param: string, dato: number): Observable<any> {
    return this.http.patch<any>(`${this.ENDPOINT}/change/rol/${param}`, {dato})
      .pipe(
        map(() => true),
        catchError((error) => {
          return of(error.status)
        })
      );
  }

  savePassw(datos: IPassw): Observable<any> {
    return this.http.patch<any>(`${this.ENDPOINT}/change/passw/${this.srvAuth.valorUsrActual.idUsuario}`, datos)
      .pipe(
        map(() => true),
        catchError((error) => {
          return of(error.status)
        })
      );
  }

  resetPassw(idUsuario: string): Observable<any> {
    return this.http.patch<any>(`${this.ENDPOINT}/reset/passw/${idUsuario}`, {})
      .pipe(
        map(() => true),
        catchError((error) => {
          return of(error.status)
        })
      );
  }

  // Manejo de errores centralizado
  private handleError(error: any) {
    // Aquí puedes personalizar el manejo de errores según tus necesidades
    console.error('Status:' + error.status + ' | Ocurrió un error:', error);
    return throwError(() => new Error('Algo salió mal; por favor intenta nuevamente más tarde.'));
  }

  constructor() { }
}