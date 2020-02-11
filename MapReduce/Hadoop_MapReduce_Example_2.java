package edu.gatech.cse6242;

import org.apache.hadoop.fs.Path;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.io.*;
import org.apache.hadoop.mapreduce.*;
import org.apache.hadoop.util.*;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import java.io.IOException;
import java.util.StringTokenizer;

public class Q4 {

  public static class NodeMapper1
	extends Mapper<Object, Text, IntWritable, IntWritable>{

    private final static IntWritable out_weight = new IntWritable(1);
    private final static IntWritable in_weight = new IntWritable(-1);
    private IntWritable src = new IntWritable();
    private IntWritable tgt = new IntWritable();

    public void map(Object key, Text value, Context context)
		throws IOException, InterruptedException{
	StringTokenizer nodeline = new StringTokenizer(value.toString());
	while (nodeline.hasMoreTokens()){
	   src.set(Integer.parseInt(nodeline.nextToken()));
	   tgt.set(Integer.parseInt(nodeline.nextToken()));
	   context.write(src, out_weight);
	   context.write(tgt, in_weight);
	}
    }
  }

  public static class NodeReducer1
	extends Reducer<IntWritable, IntWritable, IntWritable, IntWritable> {

    private IntWritable diff = new IntWritable();
    public void reduce(IntWritable key, Iterable<IntWritable> values, Context context)
		throws IOException, InterruptedException{
      int sum = 0;
      for (IntWritable val : values){
        sum += val.get();
      }
      diff.set(sum);
      context.write(key, diff);
    }
  }

  public static class NodeMapper2
	extends Mapper<Object, Text, IntWritable, IntWritable>{

    private IntWritable node = new IntWritable();
    private IntWritable diff = new IntWritable();
    private final static IntWritable count = new IntWritable(1);

    public void map(Object key, Text value, Context context)
		throws IOException, InterruptedException{
	StringTokenizer nodeline = new StringTokenizer(value.toString());
	while (nodeline.hasMoreTokens()){
	   node.set(Integer.parseInt(nodeline.nextToken()));
	   diff.set(Integer.parseInt(nodeline.nextToken()));
	   context.write(diff, count);
	}
    }
  }

  public static class NodeReducer2
	extends Reducer<IntWritable, IntWritable, IntWritable, IntWritable> {

    private IntWritable count = new IntWritable();
    public void reduce(IntWritable key, Iterable<IntWritable> values, Context context)
		throws IOException, InterruptedException{
      int sum = 0;
      for (IntWritable val : values){
        sum += val.get();
      }
      count.set(sum);
      context.write(key, count);
    }
  }

  public static void main(String[] args) throws Exception {
    Configuration conf = new Configuration();
    FileSystem hdfs = FileSystem.get(conf);

    Job job1 = Job.getInstance(conf, "Q4_1");
    job1.setJarByClass(Q4.class);
    job1.setMapperClass(NodeMapper1.class);
    job1.setCombinerClass(NodeReducer1.class);
    job1.setReducerClass(NodeReducer1.class);
    job1.setOutputKeyClass(IntWritable.class);
    job1.setOutputValueClass(IntWritable.class);
    FileInputFormat.addInputPath(job1, new Path(args[0]));
    FileOutputFormat.setOutputPath(job1, new Path("temp"));
    job1.waitForCompletion(true);

    Job job2 = Job.getInstance(conf, "Q4_2");
    job2.setJarByClass(Q4.class);
    job2.setMapperClass(NodeMapper2.class);
    job2.setCombinerClass(NodeReducer2.class);
    job2.setReducerClass(NodeReducer2.class);
    job2.setOutputKeyClass(IntWritable.class);
    job2.setOutputValueClass(IntWritable.class);
    FileInputFormat.addInputPath(job2, new Path("temp"));
    FileOutputFormat.setOutputPath(job2, new Path(args[1]));
    job2.waitForCompletion(true);
    hdfs.delete(new Path("temp"));
    System.exit(0);
  }
}
