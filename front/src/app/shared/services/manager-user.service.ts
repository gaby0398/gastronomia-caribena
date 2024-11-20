import { inject, Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { catchError, map, Observable, retry, switchMap, throwError } from 'rxjs';
import { Role, TypeClient, TypeUser } from '../models/interface';


const _SERVER = environment.servidor;

@Injectable({
  providedIn: 'root'
})

export class ManagerUserService {
  private readonly http = inject(HttpClient);
  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'Application/json'
    })
  };

  getManagerUser(userParam: any): Observable<TypeUser> {
    return this.http.get<any>(`${_SERVER}/api/usr/getUser/${userParam}`);
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

        return this.http.get<any>(`${_SERVER}/api/${rolePath}/read`).pipe(
          retry(1),
          catchError(this.handleError)
        );
      })
    );
  }

  getUser(userParam: any, id: number): Observable<TypeClient> {
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

        return this.http.get<any>(`${_SERVER}/api/${rolePath}/read/${id}`).pipe(
          retry(1),
          catchError(this.handleError)
        );
      })
    );
  }

  saveUser(userParam: any, datos: TypeClient, id?: number): Observable<any> {
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

        const url = id
          ? `${_SERVER}/api/${rolePath}/${id}`
          : `${_SERVER}/api/${rolePath}`;
        const method = id
          ? this.http.put<any>(url, datos)
          : this.http.post<any>(url, datos);

        return method.pipe(
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

        return this.http.get<any>(`${_SERVER}/api/${rolePath}/filtro`, { params }).pipe(
          retry(1),
          catchError(this.handleError)
        );
      })
    );
  }

  DeleteUser(userParam: any, id: number): Observable<boolean> {
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
        return this.http.delete<any>(`${_SERVER}/api/${rolePath}/${id}`).pipe(
          retry(1),
          map(() => true),
          catchError(this.handleError)
        );
      })
    );
  }

  private handleError(error: any) {
    return throwError(
      () => {
        return error.status;
      })
  }

  constructor() { }
}