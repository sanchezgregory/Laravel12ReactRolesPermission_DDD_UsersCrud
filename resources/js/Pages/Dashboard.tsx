import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import {
    Activity,
    ArrowDownRight,
    ArrowUpRight,
    CreditCard,
    DollarSign,
    Eye,
    Package,
    ShoppingCart,
    TrendingDown,
    TrendingUp,
    Users
} from 'lucide-react';
import {
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis
} from 'recharts';

const salesData = [
    { month: 'Ene', sales: 4500, revenue: 12000 },
    { month: 'Feb', sales: 5200, revenue: 14500 },
    { month: 'Mar', sales: 4800, revenue: 13200 },
    { month: 'Abr', sales: 6100, revenue: 16800 },
    { month: 'May', sales: 7200, revenue: 19500 },
    { month: 'Jun', sales: 6800, revenue: 18200 },
  ];
  
  const pieData = [
    { name: 'Productos', value: 45, color: '#8884d8' },
    { name: 'Servicios', value: 30, color: '#82ca9d' },
    { name: 'Consultorías', value: 25, color: '#ffc658' },
  ];
  
  const recentTransactions = [
    {
      id: 1,
      customer: 'María González',
      email: 'maria@email.com',
      amount: 2500,
      status: 'completed',
      date: '2025-01-15'
    },
    {
      id: 2,
      customer: 'Carlos Rodríguez',
      email: 'carlos@email.com',
      amount: 1800,
      status: 'pending',
      date: '2025-01-14'
    },
    {
      id: 3,
      customer: 'Ana Martínez',
      email: 'ana@email.com',
      amount: 3200,
      status: 'completed',
      date: '2025-01-13'
    },
    {
      id: 4,
      customer: 'Luis Hernández',
      email: 'luis@email.com',
      amount: 950,
      status: 'failed',
      date: '2025-01-12'
    },
  ];
  
  const topProducts = [
    { name: 'Laptop Pro', sales: 145, revenue: 87000, trend: 'up' },
    { name: 'Smartphone X', sales: 234, revenue: 62000, trend: 'up' },
    { name: 'Tablet Air', sales: 89, revenue: 31000, trend: 'down' },
    { name: 'Headphones', sales: 167, revenue: 15000, trend: 'up' },
  ];
  
  const kpis = [
    {
      title: 'Ingresos Totales',
      value: '$142,500',
      change: '+12.5%',
      trend: 'up',
      icon: DollarSign,
      color: 'text-green-600'
    },
    {
      title: 'Ventas',
      value: '2,345',
      change: '+8.2%',
      trend: 'up',
      icon: ShoppingCart,
      color: 'text-blue-600'
    },
    {
      title: 'Usuarios Activos',
      value: '12,543',
      change: '+5.7%',
      trend: 'up',
      icon: Users,
      color: 'text-purple-600'
    },
    {
      title: 'Productos',
      value: '89',
      change: '-2.1%',
      trend: 'down',
      icon: Package,
      color: 'text-orange-600'
    },
  ];
  
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    return (
      <AppLayout breadcrumbs={breadcrumbs}>
        <Head title="Dashboard" />
        <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 overflow-x-auto">
          {/* KPIs Grid */}
          <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
            {kpis.map((kpi, index) => (
              <Card key={index} className="relative overflow-hidden border-0 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-sm hover:shadow-md transition-all duration-300">
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium text-muted-foreground">
                    {kpi.title}
                  </CardTitle>
                  <kpi.icon className={`h-4 w-4 ${kpi.color}`} />
                </CardHeader>
                <CardContent>
                  <div className="text-2xl font-bold">{kpi.value}</div>
                  <div className="flex items-center text-xs text-muted-foreground">
                    {kpi.trend === 'up' ? (
                      <TrendingUp className="mr-1 h-3 w-3 text-green-500" />
                    ) : (
                      <TrendingDown className="mr-1 h-3 w-3 text-red-500" />
                    )}
                    <span className={kpi.trend === 'up' ? 'text-green-500' : 'text-red-500'}>
                      {kpi.change}
                    </span>
                    <span className="ml-1">desde el mes pasado</span>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
  
          {/* Charts Section */}
          <div className="grid gap-6 md:grid-cols-2">
            {/* Sales Chart */}
            <Card className="border-0 shadow-sm">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Activity className="h-5 w-5" />
                  Ventas y Ingresos
                </CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <BarChart data={salesData}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="month" />
                    <YAxis />
                    <Tooltip />
                    <Bar dataKey="sales" fill="#8884d8" name="Ventas" />
                    <Bar dataKey="revenue" fill="#82ca9d" name="Ingresos" />
                  </BarChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
  
            {/* Revenue Distribution */}
            <Card className="border-0 shadow-sm">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <CreditCard className="h-5 w-5" />
                  Distribución de Ingresos
                </CardTitle>
              </CardHeader>
              <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                  <PieChart>
                    <Pie
                      data={pieData}
                      cx="50%"
                      cy="50%"
                      outerRadius={80}
                      fill="#8884d8"
                      dataKey="value"
                      label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                    >
                      {pieData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={entry.color} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </CardContent>
            </Card>
          </div>
  
          {/* Bottom Section */}
          <div className="grid gap-6 md:grid-cols-3">
            {/* Recent Transactions */}
            <Card className="md:col-span-2 border-0 shadow-sm">
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span className="flex items-center gap-2">
                    <Eye className="h-5 w-5" />
                    Transacciones Recientes
                  </span>
                  <Button variant="ghost" size="sm">
                    Ver todas
                  </Button>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {recentTransactions.map((transaction) => (
                    <div key={transaction.id} className="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                      <div className="flex items-center gap-3">
                        <Avatar className="h-8 w-8">
                          <AvatarFallback>
                            {transaction.customer.split(' ').map(n => n[0]).join('')}
                          </AvatarFallback>
                        </Avatar>
                        <div>
                          <p className="font-medium">{transaction.customer}</p>
                          <p className="text-sm text-muted-foreground">{transaction.email}</p>
                        </div>
                      </div>
                      <div className="flex items-center gap-3">
                        <div className="text-right">
                          <p className="font-medium">${transaction.amount.toLocaleString()}</p>
                          <p className="text-sm text-muted-foreground">{transaction.date}</p>
                        </div>
                        <Badge 
                          variant={
                            transaction.status === 'completed' ? 'default' :
                            transaction.status === 'pending' ? 'secondary' : 'destructive'
                          }
                        >
                          {transaction.status}
                        </Badge>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
  
            {/* Top Products */}
            <Card className="border-0 shadow-sm">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Package className="h-5 w-5" />
                  Productos Top
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {topProducts.map((product, index) => (
                    <div key={index} className="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <p className="font-medium">{product.name}</p>
                          {product.trend === 'up' ? (
                            <ArrowUpRight className="h-3 w-3 text-green-500" />
                          ) : (
                            <ArrowDownRight className="h-3 w-3 text-red-500" />
                          )}
                        </div>
                        <p className="text-sm text-muted-foreground">{product.sales} ventas</p>
                        <div className="mt-2">
                          <Progress value={(product.sales / 250) * 100} className="h-1" />
                        </div>
                      </div>
                      <div className="text-right ml-4">
                        <p className="font-medium">${product.revenue.toLocaleString()}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </AppLayout>
    );
  }